<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminBaseController;
use App\Services\GalleryImages\GalleryImageService;
use App\Services\GalleryAlbums\GalleryAlbumService;
use App\Http\Requests\GalleryImages\StoreGalleryImageRequest as StoreRequest;
use App\Http\Requests\GalleryImages\UpdateGalleryImageRequest as UpdateRequest;

class GalleryImageController extends AdminBaseController
{
    protected GalleryImageService $service;
    protected GalleryAlbumService $albumService;

    public function __construct(GalleryImageService $service, GalleryAlbumService $albumService)
    {
        $this->service = $service;
        $this->albumService = $albumService;
    }

    // list all items
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'album_id']);
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

        return view('admin.gallery_images.index', [
            'page_title' => 'Gallery Images',
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
        $albums = $this->albumService->getIdTitle();
        
        return view('admin.gallery_images.create', [
            'albums' => $albums,
        ]);
    }

    // handle add form submission
    public function store(StoreRequest $request)
    {
        $this->service->store($request->validated());

        return $this->successResponse('Gallery image created successfully');
    }

    // show edit form (AJAX modal)
    public function edit($id)
    {
        $image = $this->service->find($id);
        $albums = $this->albumService->getIdTitle();

        return view('admin.gallery_images.edit', [
            'edit_data'  => $image,
            'albums' => $albums,
        ]);
    }

    // handle edit form submission
    public function update(UpdateRequest $request, $id)
    {
        $this->service->update($id, $request->validated());

        return $this->successResponse('Gallery image updated successfully');
    }

    // show single image
    public function show($id)
    {
        return view('admin.gallery_images.show', [
            'item'     => $this->service->find($id),
        ]);
    }

    // show sort view
    public function sortView(Request $request)
    {
        return view('admin.gallery_images.sort', [
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

    // delete an image
    public function destroy($id)
    {
        if (!$this->service->delete($id)) {
            return $this->errorResponse('Failed to delete gallery image');
        }
        return $this->successResponse('Gallery image deleted successfully');
    }

    // bulk delete images
    public function bulkDelete(Request $request)
    {
        if (!$this->service->bulkDelete($request->ids)) {
            return $this->errorResponse('Failed to delete gallery images');
        }
        return $this->successResponse('Selected gallery images deleted successfully');
    }

    // clone item
    public function cloneItem($id)
    {
        $image = $this->service->find($id);

        $clone = $this->service->clone($image, [
            'title' => $image->title ? $image->title . ' (Copy)' : null,
        ]);

        if (!$clone) {
            return $this->errorResponse('Failed to clone gallery image');
        }

        return $this->successResponse('Gallery image cloned successfully.', [
            'action'  => 'modal', // or 'redirect'
            'url'     => route('admin.gallery-images.edit', $clone->id),
        ]);
    }
}
