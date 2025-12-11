<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Blogs\StoreBlogRequest as StoreRequest;
use App\Http\Requests\Blogs\UpdateBlogRequest as UpdateRequest;
use App\Services\Blogs\BlogService;
use Illuminate\Http\Request;

class BlogController extends AdminBaseController
{
    public function __construct(private BlogService $service) {}

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

        return view('admin.blogs.index', [
            'page_title' => 'Blogs',
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
        return view('admin.blogs.create');
    }

    // handle add form submission
    public function store(StoreRequest $request)
    {
        $this->service->store($request->validated());

        return $this->successResponse('Blog created successfully');
    }

    // show edit form (AJAX modal)
    public function edit($id)
    {
        $blog = $this->service->find($id);

        return view('admin.blogs.edit', [
            'edit_data'  => $blog,
        ]);
    }

    // handle edit form submission
    public function update(UpdateRequest $request, $id)
    {
        $this->service->update($id, $request->validated());

        return $this->successResponse('Blog updated successfully');
    }

    // show single blog
    public function show($id)
    {
        return view('admin.blogs.show', [
            'item'     => $this->service->find($id),
        ]);
    }

    // show sort view
    public function sortView(Request $request)
    {
        return view('admin.blogs.sort', [
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

    // delete a blog
    public function destroy($id)
    {
        if (!$this->service->delete($id)) {
            return $this->errorResponse('Failed to delete blog');
        }
        return $this->successResponse('Blog deleted successfully');
    }

    // bulk delete blogs
    public function bulkDelete(Request $request)
    {
        if (!$this->service->bulkDelete($request->ids)) {
            return $this->errorResponse('Failed to delete blogs');
        }
        return $this->successResponse('Selected blogs deleted successfully');
    }

    // clone item
    public function cloneItem($id)
    {
        $blog = $this->service->find($id);

        $cloned = $this->service->clone($blog);

        if (!$cloned) {
            return $this->errorResponse('Failed to clone blog.');
        }

        return $this->successResponse('Blog cloned successfully.', [
            'action'  => 'modal',
            'url'     => route('admin.blogs.edit', $cloned->id),
        ]);
    }
}