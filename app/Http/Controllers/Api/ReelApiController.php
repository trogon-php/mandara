<?php

namespace App\Http\Controllers\Api;

use App\Services\Reels\ReelService;
use App\Http\Resources\Reels\AppReelResource;
use Illuminate\Http\Request;

class ReelApiController extends BaseApiController
{
    protected ReelService $service;

    public function __construct(ReelService $service)
    {
        $this->service = $service;
    }

    /**
     * Get paginated reels
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $reels = $this->service->paginate($perPage);
        
        $reels = AppReelResource::collection($reels);
            
        return $this->respondPaginated($reels, 'Reels fetched successfully');
    }
}
