<?php

namespace App\Http\Resources\Blogs;

use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;

class AppBlogsListResource extends BaseResource
{
    protected bool $includeId = true;
    protected bool $includeAudit = true;
    
    public function __construct($resource)
    {
        parent::__construct($resource);
    }

    protected function resourceFields(Request $request): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'image_url' => $this->image_url,
            'short_description' => $this->short_description,
        ];
    }
}
