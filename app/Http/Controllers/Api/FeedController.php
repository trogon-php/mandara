<?php

namespace App\Http\Controllers\Api;

use App\Services\Feeds\FeedService;
use App\Http\Resources\Feeds\AppFeedResource;
use Illuminate\Http\Request;

class FeedController extends BaseApiController
{
    protected FeedService $service;

    public function __construct(FeedService $service)
    {
        $this->service = $service;
    }

    /**
     * Get paginated feeds
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $feeds = $this->service->paginate($perPage);
        
        // $feeds->getCollection()->transform(function ($feed) {
        //     return new AppFeedResource($feed);
        // });
        $feeds = AppFeedResource::collection($feeds);
        
        return $this->respondPaginated($feeds, 'Feeds fetched successfully');
    }
}
