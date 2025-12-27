<?php

namespace App\Http\Resources\MemoryJournals;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class AppMemoryJournalResource extends BaseResource
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
            'date' => $this->date ? $this->date->format('D j F Y') : null,
            // 'image' => $this->image,
            'image_url' => $this->image_url,
            'content' => $this->content,
        ];
    }
}

