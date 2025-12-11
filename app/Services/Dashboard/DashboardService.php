<?php

namespace App\Services\Dashboard;

use App\Services\Core\BaseService;
use App\Models\User;
use App\Services\Traits\CacheableService;
use Illuminate\Support\Facades\Log;

class DashboardService extends BaseService
{
    // CacheableService trait
    use CacheableService;
    protected string $cachePrefix = 'dashboard';
    protected int $cacheTtl = 300; // 5 minutes

    /**
     * Get comprehensive dashboard data (cached)
     */
    public function getDashboardData(): array
    {
        return $this->remember('dashboard_data', function () {
            return [
                // Statistics data
                'number_of_students' => $this->getStudentCount(),
                'number_of_instructors' => $this->getInstructorCount(),
                'new_students_today' => $this->getNewStudentsToday(),
                'new_students_week' => $this->getNewStudentsThisWeek(),
                // 'purchased_students' => $this->getPurchasedStudentsCount(),
                // 'not_purchased_students' => $this->getNotPurchasedStudentsCount(),
                
                // Financial data
                'total_income' => $this->getTotalIncome(),
                'total_income_month' => $this->getMonthlyIncome(),
                'income_growth_percentage' => $this->getIncomeGrowthPercentage(),
                'monthly_income_growth_percentage' => $this->getMonthlyIncomeGrowthPercentage(),
                
                // Registration data
                'total_registrations' => $this->getTotalRegistrations(),
                
                // Additional data for charts and analytics
                'monthly_profits' => $this->getMonthlyProfits(),
                'student_registrations' => $this->getMonthlyStudentRegistrations(),
            ];
        });
    }

    /**
     * Clear dashboard cache
     */
    public function clearDashboardCache(): void
    {
        $this->forget('dashboard_data');
    }

    /**
     * Get student count
     */
    public function getStudentCount(): int
    {
        try {
            return User::where('role_id', 2)->count();
        } catch (\Exception $e) {
            Log::error('Error getting student count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get instructor count
     */
    public function getInstructorCount(): int
    {
        try {
            return User::where('role_id', 3)->count();
        } catch (\Exception $e) {
            Log::error('Error getting instructor count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get new students registered today
     */
    public function getNewStudentsToday(): int
    {
        try {
            return User::where('role_id', 2)
                ->whereDate('created_at', today())
                ->count();
        } catch (\Exception $e) {
            Log::error('Error getting new students today: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get new students registered this week
     */
    public function getNewStudentsThisWeek(): int
    {
        try {
            return User::where('role_id', 2)
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count();
        } catch (\Exception $e) {
            Log::error('Error getting new students this week: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get count of students who have purchased courses
     */
    public function getPurchasedStudentsCount(): int
    {
        try {
            return Enrollment::where('type', 'paid')
                ->where('status', 'active')
                ->distinct('user_id')
                ->count('user_id');
        } catch (\Exception $e) {
            Log::error('Error getting purchased students count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get count of students who haven't purchased any courses
     */
    public function getNotPurchasedStudentsCount(): int
    {
        try {
            $totalStudents = $this->getStudentCount();
            $purchasedStudents = $this->getPurchasedStudentsCount();
            return max(0, $totalStudents - $purchasedStudents);
        } catch (\Exception $e) {
            Log::error('Error getting not purchased students count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get total income from all paid enrollments
     */
    public function getTotalIncome(): string
    {
        try {
            $totalIncome = $this->calculateTotalIncome();
            return number_format($totalIncome);
        } catch (\Exception $e) {
            Log::error('Error getting total income: ' . $e->getMessage());
            return '0';
        }
    }

    /**
     * Get monthly income for current month
     */
    public function getMonthlyIncome(): string
    {
        try {
            $monthlyIncome = $this->calculateMonthlyIncome(
                now()->startOfMonth(),
                now()->endOfMonth()
            );
            return number_format($monthlyIncome);
        } catch (\Exception $e) {
            Log::error('Error getting monthly income: ' . $e->getMessage());
            return '0';
        }
    }

    /**
     * Get income growth percentage compared to last year
     */
    public function getIncomeGrowthPercentage(): string
    {
        try {
            $currentYearIncome = $this->calculateYearlyIncome(now()->year);
            $lastYearIncome = $this->calculateYearlyIncome(now()->year - 1);
            
            if ($lastYearIncome == 0) {
                return '0.0';
            }
            
            $growthPercentage = (($currentYearIncome - $lastYearIncome) / $lastYearIncome) * 100;
            return number_format($growthPercentage, 1);
        } catch (\Exception $e) {
            Log::error('Error getting income growth percentage: ' . $e->getMessage());
            return '0.0';
        }
    }

    /**
     * Get monthly income growth percentage compared to last month
     */
    public function getMonthlyIncomeGrowthPercentage(): string
    {
        try {
            $currentMonthIncome = $this->calculateMonthlyIncome(
                now()->startOfMonth(),
                now()->endOfMonth()
            );
            
            $lastMonthIncome = $this->calculateMonthlyIncome(
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth()
            );
            
            if ($lastMonthIncome == 0) {
                return '0.0';
            }
            
            $growthPercentage = (($currentMonthIncome - $lastMonthIncome) / $lastMonthIncome) * 100;
            return number_format($growthPercentage, 1);
        } catch (\Exception $e) {
            Log::error('Error getting monthly income growth percentage: ' . $e->getMessage());
            return '0.0';
        }
    }

    /**
     * Get total student registrations
     */
    public function getTotalRegistrations(): string
    {
        try {
            $totalRegistrations = User::where('role_id', 2)->count();
            return number_format($totalRegistrations);
        } catch (\Exception $e) {
            Log::error('Error getting total registrations: ' . $e->getMessage());
            return '0';
        }
    }

    /**
     * Get monthly student registrations for the last 12 months
     */
    public function getMonthlyStudentRegistrations(): array
    {
        try {
            $startDate = now()->subMonths(11)->startOfMonth();
            $endDate = now()->endOfMonth();
            
            $registrations = User::where('role_id', 2)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
                ->groupBy('year', 'month')
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc')
                ->get()
                ->keyBy(function ($item) {
                    return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
                });
            
            $monthlyData = [];
            
            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $key = $date->format('Y-m');
                $monthlyData[] = $registrations->get($key, 0)->count ?? 0;
            }
            
            return $monthlyData;
        } catch (\Exception $e) {
            Log::error('Error getting monthly student registrations: ' . $e->getMessage());
            return [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        }
    }

    /**
     * Get monthly purchased courses for the last 12 months
     */
    public function getMonthlyPurchasedCourses(): array
    {
        try {
            $startDate = now()->subMonths(11)->startOfMonth();
            $endDate = now()->endOfMonth();
            
            $purchasedCourses = Enrollment::where('type', 'paid')
                ->where('status', 'active')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
                ->groupBy('year', 'month')
                ->orderBy('year', 'asc')
                ->orderBy('month', 'asc')
                ->get()
                ->keyBy(function ($item) {
                    return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
                });
            
            $monthlyData = [];
            
            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $key = $date->format('Y-m');
                $monthlyData[] = $purchasedCourses->get($key, 0)->count ?? 0;
            }
            
            return $monthlyData;
        } catch (\Exception $e) {
            Log::error('Error getting monthly purchased courses: ' . $e->getMessage());
            return [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        }
    }

    /**
     * Get monthly profits for the last 12 months
     */
    public function getMonthlyProfits(): array
    {
        try {
            $monthlyData = [];
            
            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $startOfMonth = $date->copy()->startOfMonth();
                $endOfMonth = $date->copy()->endOfMonth();
                
                $monthlyIncome = $this->calculateMonthlyIncome($startOfMonth, $endOfMonth);
                $monthlyData[] = $monthlyIncome;
            }
            
            return $monthlyData;
        } catch (\Exception $e) {
            Log::error('Error getting monthly profits: ' . $e->getMessage());
            return [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        }
    }

    /**
     * Get dynamic course statistics
     */
    public function getCourseStatistics(): array
    {
        try {
            $courses = $this->courseService->getAll()
                ->where('status', 'published')
                ->take(10)
                ->map(function ($course) {
                    $enrollmentStats = $this->getCourseEnrollmentStats($course->id);
                    
                    return [
                        'title' => $course->title,
                        'students' => $enrollmentStats['total_students'],
                        'purchased' => $enrollmentStats['paid_students'],
                        'income' => $enrollmentStats['estimated_income'],
                    ];
                })
                ->sortByDesc('students')
                ->take(5)
                ->values()
                ->toArray();
                
            return $courses;
        } catch (\Exception $e) {
            Log::error('Error getting course statistics: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get course data specifically formatted for charts
     */
    public function getCourseChartData(): array
    {
        try {
            $courseData = $this->getCourseStatistics();
            
            if (empty($courseData)) {
                return [
                    'series' => [],
                    'labels' => [],
                    'colors' => ['#357980', '#27474A', '#FFC107', '#F44336', '#2196F3']
                ];
            }
            
            return [
                'series' => array_column($courseData, 'students'),
                'labels' => array_column($courseData, 'title'),
                'colors' => ['#357980', '#27474A', '#FFC107', '#F44336', '#2196F3']
            ];
        } catch (\Exception $e) {
            Log::error('Error getting course chart data: ' . $e->getMessage());
            return [
                'series' => [],
                'labels' => [],
                'colors' => ['#357980', '#27474A', '#FFC107', '#F44336', '#2196F3']
            ];
        }
    }

    /**
     * Get comprehensive course analytics
     */
    public function getCourseAnalytics(): array
    {
        try {
            $totalCourses = $this->courseService->getAll()
                ->where('status', 'published')
                ->count();
            
            $totalStudents = Enrollment::where('status', 'active')
                ->distinct('user_id')
                ->count('user_id');
            
            $totalPaidEnrollments = Enrollment::where('type', 'paid')
                ->where('status', 'active')
                ->count();
            
            $averageStudentsPerCourse = $totalCourses > 0 ? round($totalStudents / $totalCourses, 1) : 0;
            
            $totalEnrollments = Enrollment::where('status', 'active')->count();
            $conversionRate = $totalEnrollments > 0 ? round(($totalPaidEnrollments / $totalEnrollments) * 100, 1) : 0;
            
            return [
                'total_courses' => $totalCourses,
                'total_students' => $totalStudents,
                'total_paid_enrollments' => $totalPaidEnrollments,
                'average_students_per_course' => $averageStudentsPerCourse,
                'conversion_rate' => $conversionRate,
                'total_enrollments' => $totalEnrollments
            ];
        } catch (\Exception $e) {
            Log::error('Error getting course analytics: ' . $e->getMessage());
            return [
                'total_courses' => 0,
                'total_students' => 0,
                'total_paid_enrollments' => 0,
                'average_students_per_course' => 0,
                'conversion_rate' => 0,
                'total_enrollments' => 0
            ];
        }
    }

    /**
     * Get enrollment statistics for a specific course
     */
    private function getCourseEnrollmentStats(int $courseId): array
    {
        $totalStudents = Enrollment::where('course_id', $courseId)
            ->where('status', 'active')
            ->count();
        
        $paidStudents = Enrollment::where('course_id', $courseId)
            ->where('status', 'active')
            ->where('type', 'paid')
            ->count();
        
        $estimatedIncome = $this->calculateCourseIncome($courseId);
        
        return [
            'total_students' => $totalStudents,
            'paid_students' => $paidStudents,
            'estimated_income' => $estimatedIncome,
        ];
    }

    /**
     * Calculate estimated income for a course
     */
    private function calculateCourseIncome(int $courseId): int
    {
        $paidEnrollments = Enrollment::where('course_id', $courseId)
            ->where('type', 'paid')
            ->where('status', 'active')
            ->count();
        
        $averagePrice = CourseUnit::where('course_id', $courseId)
            ->where('access_type', 'paid')
            ->avg('price') ?? 0;
        
        return (int) ($paidEnrollments * $averagePrice);
    }

    /**
     * Calculate estimated income for a specific month
     */
    private function calculateMonthlyIncome($startDate, $endDate): int
    {
        $paidEnrollments = Enrollment::where('type', 'paid')
            ->where('status', 'active')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
        
        $totalIncome = 0;
        
        foreach ($paidEnrollments as $enrollment) {
            $averagePrice = CourseUnit::where('course_id', $enrollment->course_id)
                ->where('access_type', 'paid')
                ->avg('price') ?? 0;
            
            $totalIncome += $averagePrice;
        }
        
        return (int) $totalIncome;
    }

    /**
     * Calculate total income from all paid enrollments
     */
    private function calculateTotalIncome(): int
    {
        $paidEnrollments = Enrollment::where('type', 'paid')
            ->where('status', 'active')
            ->get();
        
        $totalIncome = 0;
        
        foreach ($paidEnrollments as $enrollment) {
            $averagePrice = CourseUnit::where('course_id', $enrollment->course_id)
                ->where('access_type', 'paid')
                ->avg('price') ?? 0;
            
            $totalIncome += $averagePrice;
        }
        
        return (int) $totalIncome;
    }

    /**
     * Calculate yearly income for a specific year
     */
    private function calculateYearlyIncome(int $year): int
    {
        $startOfYear = now()->setYear($year)->startOfYear();
        $endOfYear = now()->setYear($year)->endOfYear();
        
        $paidEnrollments = Enrollment::where('type', 'paid')
            ->where('status', 'active')
            ->whereBetween('created_at', [$startOfYear, $endOfYear])
            ->get();
        
        $totalIncome = 0;
        
        foreach ($paidEnrollments as $enrollment) {
            $averagePrice = CourseUnit::where('course_id', $enrollment->course_id)
                ->where('access_type', 'paid')
                ->avg('price') ?? 0;
            
            $totalIncome += $averagePrice;
        }
        
        return (int) $totalIncome;
    }


    // Required abstract methods from BaseService
    public function getFilterConfig(): array
    {
        return [];
    }

    public function getSearchFieldsConfig(): array
    {
        return [];
    }

    public function getDefaultSearchFields(): array
    {
        return [];
    }

    public function getDefaultSorting(): array
    {
        return ['field' => 'created_at', 'direction' => 'desc'];
    }
}
