<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\Estore\EstoreOrderAssignmentService;
use App\Services\Estore\EstoreOrderService;
use Illuminate\Http\Request;

class EstoreOrderController extends AdminBaseController
{
    public function __construct(
        private EstoreOrderService $service,
        private EstoreOrderAssignmentService $assignmentService
        ) {}

    public function index(Request $request)
    {
        $filters = array_filter($request->only(['order_status', 'payment_status', 'date_from', 'date_to']));
        $searchParams = ['search' => $request->get('search')];

        $list_items = $this->service->getFilteredData([
            'search' => $searchParams['search'],
            'filters' => $filters
        ]);

        // Load assignments for orders
        $list_items->load(['currentAssignment.deliveryStaff']);

        return view('admin.estore_orders.index', [
            'page_title' => 'Estore Orders',
            'list_items' => $list_items,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $this->service->getFilterConfig(),
            'searchConfig' => $this->service->getSearchConfig(),
        ]);
    }

    public function show(string $id)
    {
        $order = $this->service->find($id, ['user', 'items.product']);
        
        if (!$order) {
            return $this->errorResponse('Order not found', 404);
        }

        return view('admin.estore_orders.show', [
            'page_title' => 'Order Details',
            'order' => $order,
        ]);
    }
    /**
     * Assign order to delivery staff
     */
    public function assignOrder(Request $request, string $id)
    {
       
        $data = $request->validate([
            'delivery_staff_id' => 'required|exists:users,id',
            'delivery_room' => 'nullable|string|max:50',
        ]);

        $managerId = $this->userId();
        $result = $this->assignmentService->assignOrder(
            (int)$id,
            $data['delivery_staff_id'],
            $managerId,
            $data['delivery_room'] ?? null
        );

        if (!$result['status']) {
            return $this->errorResponse($result['message'], $result['http_code']);
        }

        return $this->successResponse(
            $result['message'], 
            $result['data']
        );
    }
}