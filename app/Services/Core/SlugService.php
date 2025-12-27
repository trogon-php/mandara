<?php

namespace App\Services\Core;

use App\Models\DietPlan;

class SlugService
{
    public function checkSlug(string $modelName, string $slug, ?int $excludeId = null): ?bool
    {
        $modelClass = $this->getModelClass($modelName);
        if (!$modelClass) {
            return null;
        }

        $query = $modelClass::where('slug', $slug);

        // Exclude current record if editing
        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }
    private function getModelClass(string $modelName): ?string
    {
        switch ($modelName) {
            case 'diet_plan':
                return DietPlan::class;
                break;
            default:
                return null;
        }
    }
}
