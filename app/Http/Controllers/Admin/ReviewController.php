<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Controllers\Admin\AdminBaseController;

use App\Services\Reviews\ReviewService;

use App\Http\Requests\Reviews\StoreReviewRequest as StoreRequest;
use App\Http\Requests\Reviews\UpdateReviewRequest as UpdateRequest;

class ReviewController extends AdminBaseController
{
    protected ReviewService $service;

    public function __construct(ReviewService $service)
    {
        $this->service = $service;
    }

    // list all items
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'rating', 'date_from', 'date_to']);
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

        return view('admin.reviews.index', [
            'page_title' => 'Reviews',
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
        return view('admin.reviews.create');
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
        $review = $this->service->find($id);

        return view('admin.reviews.edit', [
            'edit_data'  => $review,
        ]);
    }

    // handle edit form submission
    public function update(UpdateRequest $request, $id)
    {
        $this->service->update($id, $request->validated());

        return $this->successResponse('Item updated successfully');
    }

    // show single review
    public function show($id)
    {
        return view('admin.reviews.show', [
            'item'     => $this->service->find($id),
        ]);
    }

    // show sort view
    public function sortView(Request $request)
    {
        return view('admin.reviews.sort', [
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

    // delete a review
    public function destroy($id)
    {
        if (!$this->service->delete($id)) {
            return $this->errorResponse('Failed to delete item');
        }
        return $this->successResponse('Item deleted successfully');
    }

    // bulk delete reviews
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
        $review = $this->service->find($id);

        $cloned = $this->service->clone($review);

        if (!$cloned) {
            return $this->errorResponse('Failed to clone item.');
        }

        return $this->successResponse('Item cloned successfully.', [
            'action'  => 'modal', // or 'redirect'
            'url'     => route('admin.reviews.edit', $cloned->id),
        ]);
    }
}
