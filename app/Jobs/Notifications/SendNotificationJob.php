<?php

namespace App\Jobs\Notifications;

use App\Jobs\BaseJob;
use App\Services\Notifications\NotificationSenderService;
use Illuminate\Support\Facades\Log;

class SendNotificationJob extends BaseJob
{
    public function __construct(
        private array $userIds,
        private string $title,
        private string $body,
        private array $data = [],
        private array $channels = ['firebase']
    ) {
        //
    }

    public function handle(): void
    {
        try {
            Log::info('Processing notification batch job', [
                'user_count' => count($this->userIds),
                'channels' => $this->channels,
                'batch_info' => [
                    'first_user_id' => $this->userIds[0] ?? null,
                    'last_user_id' => end($this->userIds),
                ],
            ]);
            $results = app(NotificationSenderService::class)->send(
                $this->userIds,
                $this->title,
                $this->body,
                $this->channels,
                $this->data
            );

            Log::info('Notification batch job completed', [
                'user_count' => count($this->userIds),
                'channels' => $this->channels,
                'results' => $results,
            ]);
        } catch (\Exception $e) {
            Log::error('Notification batch job failed', [
                'error' => $e->getMessage(),
                'user_ids_count' => count($this->userIds),
                'user_ids_sample' => array_slice($this->userIds, 0, 10), // Log first 10 for debugging
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    public function getQueueName(): string
    {
        return 'high';
    }
}
