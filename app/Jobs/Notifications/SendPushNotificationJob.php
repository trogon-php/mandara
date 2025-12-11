<?php

namespace App\Jobs\Notifications;

use App\Jobs\BaseJob;
use App\Services\Notifications\FirebasePushService;
use Illuminate\Support\Facades\Log;

class SendPushNotificationJob extends BaseJob
{


    public function __construct(
        private array $notifications,
        private string $type = 'custom'
    ) {
        //
    }
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Processing push notification job', [
                'type' => $this->type,
                'notification_count' => count($this->notifications),
            ]);

            $firebaseService = app(FirebasePushService::class);
            
            if ($this->type === 'simple') {
                $this->handleSimpleNotification($firebaseService);
            } else {
                $this->handleCustomNotification($firebaseService);
            }

            Log::info('Push notification job completed', [
                'type' => $this->type,
                'notification_count' => count($this->notifications),
            ]);
        } catch (\Exception $e) {
            Log::error('Push notification job failed', [
                'type' => $this->type,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle simple notification (same message for all users)
     */
    protected function handleSimpleNotification(FirebasePushService $firebaseService): void
    {
        $userIds = $this->notifications['user_ids'] ?? [];
        $title = $this->notifications['title'] ?? '';
        $body = $this->notifications['body'] ?? '';
        $data = $this->notifications['data'] ?? [];

        if (empty($userIds) || empty($title) || empty($body)) {
            Log::warning('Invalid simple notification data', [
                'user_ids_count' => count($userIds),
                'has_title' => !empty($title),
                'has_body' => !empty($body),
            ]);
            return;
        }

        $result = $firebaseService->send($userIds, $title, $body, $data);
        
        Log::info('Simple push notification sent', [
            'user_count' => count($userIds),
            'result' => $result,
        ]);
    }

    /**
     * Handle custom notification (different message per user)
     */
    protected function handleCustomNotification(FirebasePushService $firebaseService): void
    {
        if (empty($this->notifications)) {
            Log::warning('Empty custom notifications array');
            return;
        }

        // Validate notification structure
        $validNotifications = [];
        foreach ($this->notifications as $notification) {
            if (!isset($notification['user_id']) || 
                !isset($notification['title']) || 
                !isset($notification['body'])) {
                Log::warning('Invalid notification structure', [
                    'notification' => $notification,
                ]);
                continue;
            }

            $validNotifications[] = [
                'user_id' => $notification['user_id'],
                'title' => $notification['title'],
                'body' => $notification['body'],
                'data' => $notification['data'] ?? [],
            ];
        }

        if (empty($validNotifications)) {
            Log::warning('No valid notifications to send');
            return;
        }

        $result = $firebaseService->sendCustom($validNotifications);
        
        Log::info('Custom push notification sent', [
            'notification_count' => count($validNotifications),
            'result' => $result,
        ]);
    }

    /**
     * Get the queue name.
     */
    public function getQueueName(): string
    {
        return 'high';
    }

}
