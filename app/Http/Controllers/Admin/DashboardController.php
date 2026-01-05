<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Models\MandaraBooking;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\Dashboard\DashboardService;


class DashboardController extends AdminBaseController
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $user = $this->user();
        
        // Check if user is a nurse
        if ($user->role_id === Role::NURSE) {
            return $this->nurseDashboard();
        }
        
        // Default admin dashboard
        return $this->adminDashboard();
    }
    /**
     * Admin Dashboard
     */
    private function adminDashboard()
    {
        // Get dashboard data from service
        // $dashboardData = $this->dashboardService->getDashboardData();
        $dashboardData = [];
        // Add page metadata
        $dashboardData['page_title'] = 'Admin Dashboard';
        $dashboardData['page_name'] = 'dashboard';
        $dashboardData['user'] = $this->user();
        $dashboardData['number_of_bookings'] = 32;
        $dashboardData['approved_bookings'] = 5;
        $dashboardData['pending_bookings'] = 10;
        $dashboardData['number_of_clients'] = 58;
        $dashboardData['new_bookings_today'] = 2;
        $dashboardData['new_bookings_week'] = 15;
        $dashboardData['latest_bookings'] = app(MandaraBooking::class)->latest()->take(5)->get();
        $dashboardData['latest_users'] = app(User::class)->where('role_id', Role::CLIENT)->latest()->take(5)->get();
        // dd($dashboardData['latest_clients']);

        return view('admin.dashboard.index', $dashboardData);
    }
    
    /**
     * Nurse Dashboard
     */
    private function nurseDashboard()
    {
        // Get nurse-specific dashboard data
        $dashboardData = [];
        $dashboardData['page_title'] = 'Nurse Dashboard';
        $dashboardData['page_name'] = 'dashboard';
        $dashboardData['user'] = $this->user();
        
        // Add nurse-specific data here
        // Example: $dashboardData['my_feeds'] = ...;
        // Example: $dashboardData['my_reels'] = ...;
        
        return view('admin.dashboard.nurse', $dashboardData);
    }
    
    /**
     * Get stats partial data
     */
    public function getStatsPartial()
    {
        $stats = [
            'total_students' => $this->dashboardService->getStudentCount(),
            'total_instructors' => $this->dashboardService->getInstructorCount(),
            'new_students_today' => $this->dashboardService->getNewStudentsToday(),
            'total_income' => $this->dashboardService->getTotalIncome()
        ];
        
        return view('admin.dashboard.partials.stats', compact('stats'));
    }
    
    /**
     * Get chart data for AJAX requests
     */
    public function getChartData(Request $request)
    {
        $period = $request->get('period', 'monthly');
        
        $chartData = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            'data' => [82, 68, 90, 110, 120, 94],
            'period' => $period
        ];
        
        return response()->json($chartData);
    }
}