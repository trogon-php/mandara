<?php

namespace App\Http\Resources\MealPackage;

use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;
use App\Services\Users\UserMetaService;

class AppMealPackageResource extends BaseResource
{
    protected function resourceFields(Request $request): array
    {
        $selected = $request->selected_meal_package_id == $this->id;

        return [
            'title' => $this->title,
            'short_description' => $this->short_description,
            'thumbnail' => !empty($this->thumbnail) ? $this->thumbnail_url : null,
            'content' => $this->content,
            'labels' => explode(',', $this->labels),
            'is_veg' => $this->is_veg,
            'selected' => $selected,
        ];
    }
}
