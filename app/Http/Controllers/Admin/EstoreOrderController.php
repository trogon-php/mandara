<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\Estore\EstoreOrderService;
use Illuminate\Http\Request;

class EstoreOrderController extends AdminBaseController
{
    public function __construct(private EstoreOrderService $service) {}

    public function index(Request $request)
    {
        $filters = array_filter($request->only(['order_status', 'payment_status', 'date_from', 'date_to']));
        $searchParams = ['search' => $request->get('search')];

        $list_items = $this->service->getFilteredData([
            'search' => $searchParams['search'],
            'filters' => $filters
        ]);

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
}