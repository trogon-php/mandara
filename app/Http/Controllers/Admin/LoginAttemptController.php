<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\LoginAttempts\LoginAttemptService;

class LoginAttemptController extends AdminBaseController
{
    protected LoginAttemptService $service;

    public function __construct(LoginAttemptService $service)
    {
        $this->service = $service;
    }

    // List all login attempts
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'channel', 'date_from', 'date_to']);
        $searchParams = [
            'search' => $request->get('search'),
        ];

        // Remove empty filters
        $filters = array_filter($filters, function($value) {
            return !empty($value);
        });

        $params = [
            'search' => $searchParams['search'],
            'filters' => $filters,
        ];

        $list_items = $this->service->getFilteredData($params);

        return view('admin.login-attempts.index', [
            'page_title' => 'Login Attempts',
            'list_items' => $list_items,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $this->service->getFilterConfig(),
            'searchConfig' => $this->service->getSearchConfig(),
        ]);
    }

    // Bulk delete login attempts
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return $this->errorResponse('No items selected for deletion');
        }

        $deleted = $this->service->bulkDelete($ids);
        
        if ($deleted > 0) {
            return $this->successResponse("Successfully deleted {$deleted} login attempt(s)");
        }
        
        return $this->errorResponse('Failed to delete login attempts');
    }

    // Delete single login attempt
    public function destroy($id)
    {
        $deleted = $this->service->delete($id);
        
        if ($deleted) {
            return $this->successResponse('Login attempt deleted successfully');
        }
        
        return $this->errorResponse('Failed to delete login attempt');
    }
}
