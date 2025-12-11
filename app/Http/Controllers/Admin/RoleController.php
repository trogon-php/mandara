<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Controllers\Admin\AdminBaseController;

use App\Services\Roles\RoleService;

use App\Http\Requests\Roles\StoreRoleRequest as StoreRequest;
use App\Http\Requests\Roles\UpdateRoleRequest as UpdateRequest;

class RoleController extends AdminBaseController
{
    protected RoleService $service;

    public function __construct(RoleService $service)
    {
        $this->service = $service;
    }

    // list all items
    public function index(Request $request)
    {
        $filters = $request->only(['status']);
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

        return view('admin.roles.index', [
            'page_title' => 'Roles',
            'list_items' => $list_items,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $this->service->getFilterConfig(),
            'searchConfig' => $this->service->getSearchConfig(),
        ]);
    }

    // show add form (AJAX modal)
    public function create()
    {
        return view('admin.roles.create');
    }

    // handle add form submission
    public function store(StoreRequest $request)
    {
        $this->service->store($request->validated());

        return $this->successResponse('Item created successfully');
    }

    // show edit form (AJAX modal)
    public function edit($id)
    {
        $role = $this->service->find($id);

        return view('admin.roles.edit', [
            'edit_data'  => $role,
        ]);
    }

    // handle edit form submission
    public function update(UpdateRequest $request, $id)
    {
        try {
            $this->service->update($id, $request->validated());
            return $this->successResponse('Item updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    // show single role
    public function show($id)
    {
        return view('admin.roles.show', [
            'item'     => $this->service->find($id),
        ]);
    }

    // show sort view
    public function sortView(Request $request)
    {
        return view('admin.roles.sort', [
            'list_items' => $this->service->getAll(),
        ]);
    }

    // handle sort update
    public function sortUpdate(Request $request)
    {
        $result = $this->service->sortUpdate($request->order);
        if (!$result) {
            return $this->errorResponse('Failed to update sort order');
        }
        return $this->successResponse('Sort order updated successfully');
    }

    // delete a role
    public function destroy($id)
    {
        try {
            if (!$this->service->delete($id)) {
                return $this->errorResponse('Failed to delete item');
            }
            return $this->successResponse('Item deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    // bulk delete roles
    public function bulkDelete(Request $request)
    {
        try {
            $deletedCount = $this->service->bulkDelete($request->ids);
            if ($deletedCount === 0) {
                return $this->errorResponse('Failed to delete items');
            }
            return $this->successResponse('Selected items deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    // clone item
    public function cloneItem($id)
    {
        $role = $this->service->find($id);

        $cloned = $this->service->clone($role);

        if (!$cloned) {
            return $this->errorResponse('Failed to clone item.');
        }

        return $this->successResponse('Item cloned successfully.', [
            'action'  => 'modal', // or 'redirect'
            'url'     => route('admin.roles.edit', $cloned->id),
        ]);
    }
}
