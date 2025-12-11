<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Controllers\Admin\AdminBaseController;

use App\Services\Notifications\NotificationService;
use App\Services\Courses\CourseService;
use App\Services\Categories\CategoryService;

use App\Http\Requests\Notifications\StoreNotificationRequest as StoreRequest;
use App\Http\Requests\Notifications\UpdateNotificationRequest as UpdateRequest;

class NotificationController extends AdminBaseController
{
    protected NotificationService $service;
    protected CourseService $courseService;
    protected CategoryService $categoryService;

    public function __construct(
        NotificationService $service,
        CourseService $courseService,
        CategoryService $categoryService
    ) {
        $this->service = $service;
        $this->courseService = $courseService;
        $this->categoryService = $categoryService;
    }

    // list all items
    public function index(Request $request)
    {
        $filters = $request->only(['premium', 'course_id', 'date_from', 'date_to']);
        $searchParams = [
            'search' => $request->get('search'),
        ];

        // Remove empty filters
        $filters = array_filter($filters, function($value) {
            return ($value != null) || ($value != '');
        });

        $params = [
            'search' => $searchParams['search'],
            'filters' => $filters,
        ];

        $list_items = $this->service->getFilteredData($params);

        return view('admin.notifications.index', [
            'page_title' => 'Notifications',
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
        $courses = [];
        $categories = [];

        $courses = $this->courseService->getIdTitle();

        if (has_feature('categories')) {
            $categories = $this->categoryService->getFlatCategoriesOptions();
        }

        return view('admin.notifications.create', [
            'courses' => $courses,
            'categories' => $categories,
        ]);
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
        $notification = $this->service->find($id);
        
        $courses = [];
        $categories = [];

        $courses = $this->courseService->getIdTitle();

        if (has_feature('categories')) {
            $categories = $this->categoryService->getFlatCategoriesOptions();
        }

        return view('admin.notifications.edit', [
            'edit_data'  => $notification,
            'courses' => $courses,
            'categories' => $categories,
        ]);
    }

    // handle edit form submission
    public function update(UpdateRequest $request, $id)
    {
        $this->service->update($id, $request->validated());

        return $this->successResponse('Item updated successfully');
    }

    // show single notification
    public function show($id)
    {
        return view('admin.notifications.show', [
            'item'     => $this->service->find($id),
        ]);
    }

    // show sort view
    public function sortView(Request $request)
    {
        return view('admin.notifications.sort', [
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

    // delete a notification
    public function destroy($id)
    {
        if (!$this->service->delete($id)) {
            return $this->errorResponse('Failed to delete item');
        }
        return $this->successResponse('Item deleted successfully');
    }

    // bulk delete notifications
    public function bulkDelete(Request $request)
    {
        if (!$this->service->bulkDelete($request->ids)) {
            return $this->errorResponse('Failed to delete items');
        }
        return $this->successResponse('Selected items deleted successfully');
    }

    // clone item
    public function cloneItem($id)
    {
        $notification = $this->service->find($id);

        $cloned = $this->service->clone($notification);

        if (!$cloned) {
            return $this->errorResponse('Failed to clone item.');
        }

        return $this->successResponse('Item cloned successfully.', [
            'action'  => 'modal', // or 'redirect'
            'url'     => route('admin.notifications.edit', $cloned->id),
        ]);
    }
}
