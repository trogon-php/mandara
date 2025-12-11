<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AdminBaseController;
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
        // Get dashboard data from service
        // $dashboardData = $this->dashboardService->getDashboardData();
        $dashboardData = [];
        // Add page metadata
        $dashboardData['page_title'] = 'Admin Dashboard';
        $dashboardData['page_name'] = 'dashboard';
        $dashboardData['user'] = $this->user();

        
        return view('admin.dashboard.index', $dashboardData);
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