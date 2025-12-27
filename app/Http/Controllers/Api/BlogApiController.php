<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\Blogs\BlogService;
use App\Http\Resources\Blogs\AppBlogResource;
use App\Http\Resources\Blogs\AppBlogsListResource;
use Illuminate\Http\Request;

class BlogApiController extends BaseApiController
{

    public function __construct(private BlogService $blogService)
    {
        parent::__construct();
    }

    /**
     * Get paginated active blogs
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $blogs = $this->blogService->getActiveBlogsPaginated($perPage);
        
        $blogs = AppBlogsListResource::collection($blogs);

        return $this->respondPaginated($blogs, 'Blogs retrieved successfully');
    }

    public function show(Request $request, $id)
    {
        // dd($id);
        $blog = $this->blogService->find($id);
        if (!$blog) {
            return $this->respondError('Blog not found', 404);
        }
        $blog = AppBlogResource::make($blog);

        return $this->respondSuccess($blog, 'Blog retrieved successfully');
    }
}
