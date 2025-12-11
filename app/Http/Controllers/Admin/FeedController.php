<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\Feeds\FeedService;
use App\Http\Requests\Feeds\StoreFeedRequest as StoreRequest;
use App\Http\Requests\Feeds\UpdateFeedRequest as UpdateRequest;
use App\Models\Feed;

class FeedController extends AdminBaseController
{
    protected FeedService $service;

    public function __construct(FeedService $service)
    {
        $this->service = $service;
    }

    // List all items with filters and search
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'feed_category_id', 'date_from', 'date_to']);
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

        return view('admin.feeds.index', [
            'page_title' => 'Feeds',
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
        // Get only parent categories (where parent_id is null) and active ones
        $categories = $this->service->getFilteredData([
            'filters' => ['feed_category_id' => null, 'status' => 'active']
        ]);
        
        return view('admin.feeds.create', [
            'categories' => $categories,
        ]);
    }

    // Handle add form submission
    public function store(StoreRequest $request)
    {
        $this->service->store($request->validated());
        return $this->successResponse('Feed added successfully');
    }

    // Show edit form (AJAX modal)
    public function edit($id)
    {
        $edit_data = $this->service->find($id);

        $categories = $this->service->getFilteredData([
            'filters' => ['feed_category_id' => null, 'status' => 'active']
        ]);

        return view('admin.feeds.edit', [
            'edit_data' => $edit_data,
            'categories' => $categories,
        ]);
    }

    // Handle edit form submission
    public function update(UpdateRequest $request, $id)
    {
        $this->service->update($id, $request->validated());
        return $this->successResponse('Feed updated successfully');
    }

    // Show single category
    public function show($id)
    {
        return view('admin.feeds.show', [
            'item' => $this->service->find($id),
        ]);
    }

    // Show sort view
    public function sortView(Request $request)
    {
        return view('admin.feeds.sort', [
            'list_items' => $this->service->getAll(),
        ]);
    }

    // Handle sort update
    public function sortUpdate(Request $request)
    {
        $result = $this->service->sortUpdate($request->order);
        return $this->successResponse('Sort order updated successfully');
    }

    // Delete a category
    public function destroy($id)
    {
        if (!$this->service->delete($id)) {
            return $this->errorResponse('Failed to delete item');
        }
        return $this->successResponse('Item deleted successfully');
    }

    // Bulk delete categories
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
        $category = $this->service->find($id);

        $cloned = $this->service->clone($category);

        if (!$cloned) {
            return $this->errorResponse('Failed to clone item.');
        }

        return $this->successResponse('Item cloned successfully.', [
            'action' => 'modal', // or 'redirect'
            'url' => route('admin.feeds.edit', $cloned->id),
        ]);
    }
}
