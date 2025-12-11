<?php

namespace App\Http\Controllers\Api;

use App\Services\Galleries\GalleryImageService;
use App\Http\Resources\Galleries\AppGalleryImageResource;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GalleryImageController extends BaseApiController
{
    protected GalleryImageService $service;

    public function __construct(GalleryImageService $service)
    {
        $this->service = $service;
    }

    /**
     * Get paginated gallery images
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $galleryImages = $this->service->paginate($perPage);
        
        // Transform the collection using through method
        $galleryImages->through(function ($galleryImage) {
            return new AppGalleryImageResource($galleryImage);
        });
        
        return $this->respondPaginated($galleryImages, 'Gallery images fetched successfully');
    }
}
