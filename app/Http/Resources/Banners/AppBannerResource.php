<?php

namespace App\Http\Resources\Banners;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class AppBannerResource extends BaseResource
{
    public function __construct($resource)
    {
        parent::__construct($resource);
    }

    protected function resourceFields(Request $request): array
    {
        return [
            'title' => $this->title,
            'image_url' => $this->image_url,
            'action_type' => $this->action_type != 'image' ? $this->action_type : null,
            'action_value' => $this->action_value == 'text' ? $this->description : $this->action_value,
            // 'action_display' => $this->action_display,
        ];
    }
}
