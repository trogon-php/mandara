<?php

namespace App\Http\Controllers\Api;

use App\Services\Reels\ReelService;
use App\Http\Resources\Reels\AppReelResource;
use App\Services\Users\ClientService;
use Illuminate\Http\Request;

class ReelApiController extends BaseApiController
{
    public function __construct(
        private ReelService $service,
        private ClientService $clientService)
    {
        parent::__construct();
    }

    /**
     * Get paginated reels
     */
    public function index(Request $request)
    {
        $userId = $this->getAuthUser()->id;

        $journeyStatus = $this->clientService->getJourneyStatus($userId);
        // dd($journeyStatus);
        if($journeyStatus == 'preparing') {
            $categoryId = 1;
        } else if($journeyStatus == 'pregnant') {
            $categoryId = 2;
        } else if($journeyStatus == 'delivered') {
            $categoryId = 3;
        } else {
            $categoryId = null;
        }
        
        $perPage = $request->get('per_page', 10);
        $reels = $this->service->getPaginatedReelsByCategory($categoryId, $perPage);
        
        $reels = AppReelResource::collection($reels);
            
        return $this->respondPaginated($reels, 'Reels fetched successfully');
    }
}
