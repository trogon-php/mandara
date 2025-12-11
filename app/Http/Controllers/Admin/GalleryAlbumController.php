<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\GalleryAlbums\GalleryAlbumService;
use App\Http\Requests\GalleryAlbums\StoreGalleryAlbumRequest as StoreRequest;
use App\Http\Requests\GalleryAlbums\UpdateGalleryAlbumRequest as UpdateRequest;

class GalleryAlbumController extends AdminBaseController
{
    protected GalleryAlbumService $service;

    public function __construct(GalleryAlbumService $service)
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

        return view('admin.gallery_albums.index', [
            'page_title' => 'Gallery Albums',
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
        return view('admin.gallery_albums.create');
    }

    // handle add form submission
    public function store(StoreRequest $request)
    {
        $this->service->store($request->validated());

        return $this->successResponse('Gallery album created successfully');
    }

    // show edit form (AJAX modal)
    public function edit($id)
    {
        $album = $this->service->find($id);

        return view('admin.gallery_albums.edit', [
            'edit_data'  => $album,
        ]);
    }

    // handle edit form submission
    public function update(UpdateRequest $request, $id)
    {
        $this->service->update($id, $request->validated());

        return $this->successResponse('Gallery album updated successfully');
    }

    // show single album
    public function show($id)
    {
        return view('admin.gallery_albums.show', [
            'item'     => $this->service->find($id),
        ]);
    }

    // show sort view
    public function sortView(Request $request)
    {
        return view('admin.gallery_albums.sort', [
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

    // delete an album
    public function destroy($id)
    {
        if (!$this->service->delete($id)) {
            return $this->errorResponse('Failed to delete gallery album');
        }
        return $this->successResponse('Gallery album deleted successfully');
    }

    // bulk delete albums
    public function bulkDelete(Request $request)
    {
        if (!$this->service->bulkDelete($request->ids)) {
            return $this->errorResponse('Failed to delete gallery albums');
        }
        return $this->successResponse('Selected gallery albums deleted successfully');
    }

    // clone item
    public function cloneItem($id)
    {
        $album = $this->service->find($id);

        $clone = $this->service->clone($album, [
            'title' => $album->title . ' (Copy)',
        ]);

        if (!$clone) {
            return $this->errorResponse('Failed to clone gallery album');
        }

        return $this->successResponse('Gallery album cloned successfully.', [
            'action'  => 'modal', // or 'redirect'
            'url'     => route('admin.gallery-albums.edit', $clone->id),
        ]);
    }
}
