<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\App\AmenityAppService;
use Illuminate\Http\Request;

class AmenityController extends BaseApiController
{
    public function __construct(private AmenityAppService $service)
    {
        parent::__construct();
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $amenities = $this->service->listAmenities($perPage);

        return $this->respondPaginated($amenities);
    }
    // Get amenity page data
    public function getAmenityPage(Request $request, $id)
    {
        $data = $this->service->amenityPage($id);

        return $this->serviceResponse($data);
    }

    public function createBooking(Request $request, $id)
    {
        // dd($request->all());
        $user = $this->getAuthUser();

        $validated = $request->validate([
            'amenity_item_ids' => 'required|array',
            'amenity_item_ids.*' => 'exists:amenity_items,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required|date_format:H:i',
            'additional_note' => 'nullable|string|max:500',
        ]);
        $validated['amenity_id'] = $id;
        $data = $this->service->createBooking($user->id, $validated);

        return $this->serviceResponse($data);
    }
}
