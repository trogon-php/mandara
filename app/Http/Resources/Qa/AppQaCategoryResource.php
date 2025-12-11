<?php

namespace App\Http\Resources\Qa;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class AppQaCategoryResource extends BaseResource
{
    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->includeId = true;
    }
    protected function resourceFields(Request $request): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
        ];
    }
}
