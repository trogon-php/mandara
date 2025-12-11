<?php

namespace App\Http\Resources\Notifications;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class AppNotificationResource extends BaseResource
{
    protected $userId;

    public function __construct($resource, $userId = null)
    {
        parent::__construct($resource);
        $this->includeId = true;
        $this->userId = $userId;
    }

    protected function resourceFields(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'is_read' => $this->getIsReadStatus(),
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }

    /**
     * Get the read status for the current user
     */
    private function getIsReadStatus(): bool
    {
        if (!$this->userId) {
            return false;
        }
        // Check if notificationReads relationship is already loaded (eager loaded)
        if ($this->resource->relationLoaded('notificationReads')) {
            $readRecord = $this->resource->notificationReads->first();
            return $readRecord ? ($readRecord->is_read == 1) : false;
        }
        return $this->isReadByUser($this->userId);
    }
}
