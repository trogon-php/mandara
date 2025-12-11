<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\Blogs\BlogService;
use App\Http\Resources\Blogs\AppBlogResource;
use Illuminate\Http\Request;

class BlogApiController extends BaseApiController
{
    protected BlogService $blogService;

    public function __construct(BlogService $blogService)
    {
        $this->blogService = $blogService;
    }

    /**
     * Get paginated active blogs
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $blogs = $this->blogService->getActiveBlogsPaginated($perPage);
        
        $blogs = AppBlogResource::collection($blogs);

        return $this->respondPaginated($blogs, 'Blogs retrieved successfully');
    }
}
