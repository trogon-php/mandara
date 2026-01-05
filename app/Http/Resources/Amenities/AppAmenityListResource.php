<?php

namespace App\Http\Resources\Amenities;

use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;

class AppAmenityListResource extends BaseResource
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
            'id' => $this->id,
            'title' => $this->title,
            'icon_url' => $this->icon_url,
            'description' => $this->description,
            'type' => 'app',
            'web_url' => ''
        ];
    }
}
