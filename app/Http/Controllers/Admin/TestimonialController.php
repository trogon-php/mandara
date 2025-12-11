<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\Testimonials\TestimonialService;
use App\Http\Requests\Testimonials\StoreTestimonialRequest as StoreRequest;
use App\Http\Requests\Testimonials\UpdateTestimonialRequest as UpdateRequest;

class TestimonialController extends AdminBaseController
{
    protected TestimonialService $service;

    public function __construct(TestimonialService $service)
    {
        $this->service = $service;
    }

    // List all items with filters and search
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

        return view('admin.testimonials.index', [
            'page_title' => 'Testimonials',
            'list_items' => $list_items,
            'filters' => $filters,
            'search_params' => $searchParams,
            'filterConfig' => $this->service->getFilterConfig(),
            'searchConfig' => $this->service->getSearchConfig(),
        ]);
    }

    // Show add form (AJAX modal)
    public function create()
    {
        return view('admin.testimonials.create');
    }

    // Handle add form submission
    public function store(StoreRequest $request)
    {
        $this->service->store($request->validated());

        return $this->successResponse('Testimonial added successfully');
    }

    // Show edit form (AJAX modal)
    public function edit($id)
    {
        $testimonial = $this->service->find($id);

        return view('admin.testimonials.edit', [
            'edit_data' => $testimonial,
        ]);
    }

    // Handle edit form submission
    public function update(UpdateRequest $request, $id)
    {
        $this->service->update($id, $request->validated());

        return $this->successResponse('Testimonial updated successfully');
    }

    // Show single testimonial
    public function show($id)
    {
        return view('admin.testimonials.show', [
            'item' => $this->service->find($id),
        ]);
    }

    // Show sort view
    public function sortView(Request $request)
    {
        return view('admin.testimonials.sort', [
            'list_items' => $this->service->getAll(),
        ]);
    }

    // Handle sort update
    public function sortUpdate(Request $request)
    {
        $result = $this->service->sortUpdate($request->order);
        if (!$result) {
            return $this->errorResponse('Failed to update sort order');
        }
        return $this->successResponse('Sort order updated successfully');
    }

    // Delete a testimonial
    public function destroy($id)
    {
        if (!$this->service->delete($id)) {
            return $this->errorResponse('Failed to delete item');
        }
        return $this->successResponse('Item deleted successfully');
    }

    // Bulk delete testimonials
    public function bulkDelete(Request $request)
    {
        if (!$this->service->bulkDelete($request->ids)) {
            return $this->errorResponse('Failed to delete items');
        }
        return $this->successResponse('Selected items deleted successfully');
    }

    // Clone item
    public function cloneItem($id)
    {
        $testimonial = $this->service->find($id);

        $cloned = $this->service->clone($testimonial);

        if (!$cloned) {
            return $this->errorResponse('Failed to clone item.');
        }

        return $this->successResponse('Item cloned successfully.', [
            'action' => 'modal', // or 'redirect'
            'url' => route('admin.testimonials.edit', $cloned->id),
        ]);
    }
}