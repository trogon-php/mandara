<?php

namespace App\Http\Controllers\Admin;

use App\Services\Media\MediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MediaController extends AdminBaseController
{
    public function __construct(private MediaService $service)
    {
    }
    public function index(Request $request)
    {
        $filters = $request->only(['file_type', 'folder', 'status']);
        $searchParams = [
            'search' => $request->get('search'),
        ];

        $filters = array_filter($filters, function($value) {
            return !empty($value);
        });

        $params = [
            'search' => $searchParams['search'],
            'filters' => $filters,
            'paginate' => true,
            'per_page' => 24, // Grid view
        ];

        $list_items = $this->service->getFilteredData($params);

        return view('admin.media.index', [
            'page_title' => 'Media Library',
            'list_items' => $list_items,
            'filters' => $filters,
            'searchConfig' => $this->service->getSearchConfig(),
            'filterConfig' => $this->service->getFilterConfig(),
        ]);
    }

    public function create()
    {
        return view('admin.media.create');
    }
    public function show(int $id)
    {
        $media = $this->service->find($id);
        return view('admin.media.show', [
            'media' => $media,
        ]);
    }

    /**
     * Upload file(s) via AJAX
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'media_files.*' => 'required|file|max:' . (config('_config.file_upload.max_file_size', 10485760) / 1024), // 10MB default
            'folder' => 'nullable|string|max:255',
        ]);
        // dd($request->all());
        $files = $request->file('media_files');
        $folder = $request->input('folder');

        if (!is_array($files)) {
            $files = [$files];
        }
        // dd($files, $folder);
        $uploadedMedia = $this->service->uploadFiles($files, $folder);

        return $this->successResponse(count($uploadedMedia) . ' file(s) uploaded successfully');
    }

    /**
     * Get file URL by ID
     */
    public function getUrl(int $id): JsonResponse
    {
        $url = $this->service->getFileUrl($id);
        
        if (!$url) {
            return response()->json([
                'success' => false,
                'message' => 'Media not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'url' => $url,
        ]);
    }

    /**
     * Get file URL by path
     */
    public function getUrlByPath(Request $request): JsonResponse
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $url = $this->service->getFileUrlByPath($request->input('path'));

        return response()->json([
            'success' => true,
            'url' => $url,
        ]);
    }

    /**
     * Update media metadata
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'alt_text' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'folder' => 'nullable|string|max:255',
        ]);

        $media = $this->service->update($id, $request->only([
            'name', 'alt_text', 'description', 'folder'
        ]));

        if (!$media) {
            return $this->errorResponse('Failed to update media');
        }

        return $this->successResponse('Media updated successfully', [
            'media' => [
                'id' => $media->id,
                'name' => $media->name,
                'url' => file_url($media->file_path, $media->file_type),
            ],
        ]);
    }

    public function destroy(int $id)
    {
        if (!$this->service->delete($id)) {
            return $this->errorResponse('Failed to delete media');
        }
        return $this->successResponse('Media deleted successfully');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        if (!$this->service->bulkDelete($request->ids)) {
            return $this->errorResponse('Failed to delete media');
        }
        return $this->successResponse('Selected media deleted successfully');
    }
}
