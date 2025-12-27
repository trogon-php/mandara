<?php

namespace App\Http\Controllers\Api;

use App\Services\Feeds\FeedService;
use App\Http\Resources\Feeds\AppFeedResource;
use App\Services\Users\ClientService;
use Illuminate\Http\Request;

class FeedController extends BaseApiController
{

    public function __construct(
        private FeedService $service,
        private ClientService $clientService
        )
    {
        parent::__construct();
    }

    /**
     * Get paginated feeds
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
        $feeds = $this->service->getPaginatedFeedsByCategory($categoryId, $perPage);
        
        $feeds = AppFeedResource::collection($feeds);
        
        return $this->respondPaginated($feeds, 'Feeds fetched successfully');
    }
}
