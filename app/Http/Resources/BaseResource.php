<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class BaseResource extends JsonResource
{
    /**
     * Toggle audit fields dynamically.
     */
    protected bool $includeAudit = false;

    protected bool $includeId = false;

    /**
     * Hide audit fields in output.
     */
    public function showAudit(): static
    {
        $this->includeAudit = true;
        return $this;
    }

    public function showId(): static
    {
        $this->includeId = true;
        return $this;
    }

    /**
     * Format date consistently.
     */
    protected function formatDate(?\Carbon\Carbon $date): ?string
    {
        return $date?->format('Y-m-d H:i:s');
    }

    /**
     * Common fields available in all resources.
     */
    protected function baseFields(): array
    {
        $fields = [];
        if ($this->includeId) {
            $fields = [
                'id' => $this->id,
            ];
        }

        if ($this->includeAudit) {
            $fields['created_at'] = $this->formatDate($this->created_at);
            $fields['updated_at'] = $this->formatDate($this->updated_at);
            $fields['deleted_at'] = $this->formatDate($this->deleted_at);

            $fields['created_by'] = $this->whenLoaded('creator', fn () => [
                'id'   => $this->creator?->id,
                'name' => $this->creator?->name,
            ]);

            $fields['updated_by'] = $this->whenLoaded('updater', fn () => [
                'id'   => $this->updater?->id,
                'name' => $this->updater?->name,
            ]);

            $fields['deleted_by'] = $this->whenLoaded('deleter', fn () => [
                'id'   => $this->deleter?->id,
                'name' => $this->deleter?->name,
            ]);
        }

        return $fields;
    }

    /**
     * Merge base fields with child-specific fields.
     */
    public function toArray(Request $request): array
    {
        return array_merge(
            $this->baseFields(),
            $this->resourceFields($request)
        );
    }

    /**
     * Each child resource must implement its fields.
     */
    abstract protected function resourceFields(Request $request): array;
}
