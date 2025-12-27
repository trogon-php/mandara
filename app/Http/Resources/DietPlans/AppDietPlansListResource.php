<?php

namespace App\Http\Resources\DietPlans;

use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;

class AppDietPlansListResource extends BaseResource
{
    protected bool $includeId = true;
    protected bool $includeAudit = false;

    public function __construct($resource)
    {
        parent::__construct($resource);
    }

    protected function resourceFields(Request $request): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'month' => $this->month,
            'image_url' => $this->image_url,
            'short_description' => $this->short_description,
        ];
    }
}
