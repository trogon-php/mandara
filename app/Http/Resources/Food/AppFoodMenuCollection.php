<?php

namespace App\Http\Resources\Food;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AppFoodMenuCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        // Group items by category
        $grouped = $this->collection->groupBy(function($menuItem) {
            return $menuItem->item->category_id;
        })->map(function($items, $categoryId) {
            $category = $items->first()->item->category;
            return [
                'title' => $category->title,
                'time' => $category->start_time->format('h:i A') . ' - ' . $category->end_time->format('h:i A'),
                'items' => $items->map(function($menuItem) {
                    return [
                        'name' => $menuItem->item->title,
                        'description' => $menuItem->item->short_description,
                        'image' => $menuItem->item->image,
                    ];
                })->values()->toArray(),
            ];
        })->values();

        return $grouped->toArray();
    }
}
