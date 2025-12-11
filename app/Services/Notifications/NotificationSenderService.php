<?php

namespace App\Services\Notifications;

use App\Services\Traits\QueueableService;
use App\Jobs\Notifications\SendNotificationJob;
use App\Jobs\Notifications\SendPushNotificationJob;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationSenderService
{
    use QueueableService;
    // Maximum users per batch job
    protected const USERS_PER_BATCH = 500;
    
    public function __construct(
        protected FirebasePushService $firebasePushService,
        protected PushNotificationTemplateService $templateService)
    {
        //
    }
    /**
     * Send notification immediately (synchronous)
     */
    public function send($users, string $title, string $body, array $channels = ['firebase'], array $data = []): array
    {
        $results = [];

        if (in_array('firebase', $channels)) {
            $results['firebase'] = $this->firebasePushService->send($users, $title, $body, $data);
        }

        if (in_array('email', $channels)) {
            $results['email'] = $this->sendEmail($users, $title, $body, $data);
        }

        if (in_array('sms', $channels)) {
            $results['sms'] = $this->sendSms($users, $title, $body, $data);
        }

        return $results;
    }

    /**
     * Send notification via queue (asynchronous)
     * Automatically splits into batches of 500 users per job
     */
    public function sendQueued($users, string $title, string $body, array $channels = ['firebase'], array $data = [], string $queue = 'high', ?int $delay = null): void
    {
        // Convert users to array of IDs
        $userIds = $this->extractUserIds($users);

        if (empty($userIds)) {
            return;
        }

        // Split users into batches
        $userBatches = array_chunk($userIds, self::USERS_PER_BATCH);
        
        $totalBatches = count($userBatches);
        
        Log::info('Dispatching notification batches', [
            'total_users' => count($userIds),
            'total_batches' => $totalBatches,
            'users_per_batch' => self::USERS_PER_BATCH,
        ]);

        // Dispatch a separate job for each batch
        foreach ($userBatches as $index => $batch) {
            $job = new SendNotificationJob($batch, $title, $body, $data, $channels);
            
            // Optional: Add small delay between batches to avoid overwhelming the queue
            $batchDelay = $delay !== null ? $delay + ($index * 2) : null;
            
            $this->dispatchJob($job, $queue, $batchDelay);
            
            Log::info('Dispatched notification batch job', [
                'batch_number' => $index + 1,
                'total_batches' => $totalBatches,
                'users_in_batch' => count($batch),
                'queue' => $queue,
                'delay' => $batchDelay,
            ]);
        }
    }

    /**
     * Send only Firebase push (immediate)
     */
    public function sendPush($users, string $title, string $body, array $data = []): array
    {
        return $this->firebasePushService->send($users, $title, $body, $data);
    }

    /**
     * Send only Firebase push (queued)
     */
    public function sendPushQueued($users, string $title, string $body, array $data = [], string $queue = 'high', ?int $delay = null): void
    {
        $this->sendQueued($users, $title, $body, ['firebase'], $data, $queue, $delay);
    }
    // ============================================
    // DYNAMIC MESSAGE METHODS (Template-based)
    // ============================================

    /**
     * Send notification using template (immediate)
     * Custom message per user using Blade template
     */
    public function sendWithTemplate( string $templateName, $users, array $variables = [], array $channels = ['firebase'], array $data = [] ): array 
    {
        // Render templates using separate template service
        $rendered = $this->templateService->render($templateName, $users, $variables);
        
        if (empty($rendered)) {
            return [];
        }

        // Convert to Firebase format
        $notifications = [];
        foreach ($rendered as $userId => $notification) {
            $notifications[] = [
                'user_id' => $userId,
                'title' => $notification['title'],
                'body' => $notification['body'],
                'data' => array_merge($data, ['template' => $templateName]),
            ];
        }

        $results = [];
        
        if (in_array('firebase', $channels)) {
            $results['firebase'] = $this->firebasePushService->sendCustom($notifications);
        }

        return $results;
    }

    /**
     * Send notification using template (queued)
     * Custom message per user using Blade template
     */
    public function sendWithTemplateQueued(string $templateName, $users, array $variables = [], array $channels = ['firebase'], array $data = [], string $queue = 'high', ?int $delay = null): void 
    {
        // Render templates using separate template service
        $rendered = $this->templateService->render($templateName, $users, $variables);
        // dd($rendered);
        if (empty($rendered)) {
            return;
        }

        // Group by user ID for batching
        $userIds = array_keys($rendered);
        $userBatches = array_chunk($userIds, self::USERS_PER_BATCH);
        // dd($userBatches);
        foreach ($userBatches as $index => $batch) {
            // Get notifications for this batch
            $batchNotifications = [];
            foreach ($batch as $userId) {
                if (isset($rendered[$userId])) {
                    $batchNotifications[] = [
                        'user_id' => $userId,
                        'title' => $rendered[$userId]['title'],
                        'body' => $rendered[$userId]['body'],
                        'data' => array_merge($data, ['template' => $templateName]),
                    ];
                }
            }

            if (empty($batchNotifications)) {
                continue;
            }
            // dd($batchNotifications);
            // Queue the batch
            $batchDelay = $delay !== null ? $delay + ($index * 2) : null;

            $this->dispatchJob(new SendPushNotificationJob($batchNotifications, 'custom'), $queue, $batchDelay);
            // dispatch(function() use ($batchNotifications) {

            //     app(FirebasePushService::class)->sendCustom($batchNotifications);

            // })->onQueue($queue)->delay($batchDelay);
        }
    }

    /**
     * Send push notification with template (immediate)
     */
    public function sendPushWithTemplate(string $templateName, $users, array $variables = [], array $data = []): array 
    {
        return $this->sendWithTemplate($templateName, $users, $variables, ['firebase'], $data);
    }

    /**
     * Send push notification with template (queued)
     */
    public function sendPushWithTemplateQueued(string $templateName, $users, array $variables = [], array $data = [], string $queue = 'high', ?int $delay = null): void 
    {
        // dd($templateName, $users, $variables, $data, $queue, $delay);
        $this->sendWithTemplateQueued($templateName, $users, $variables, ['firebase'], $data, $queue, $delay);
    }

    /**
     * Extract user IDs from various input types
     */
    protected function extractUserIds($users): array
    {
        if (is_array($users)) {
            if (empty($users)) {
                return [];
            }
            
            // Check first element safely
            $first = reset($users);
            if (is_numeric($first)) {
                return $users; // Already array of IDs
            }
            
            if ($first instanceof User) {
                return array_column($users, 'id');
            }
        }

        if ($users instanceof User) {
            return [$users->id];
        }

        if (is_numeric($users)) {
            return [$users];
        }

        // If it's a collection
        if (is_object($users) && method_exists($users, 'pluck')) {
            return $users->pluck('id')->toArray();
        }

        return [];
    }

    /**
     * Send email (placeholder)
     */
    protected function sendEmail($users, string $title, string $body, array $data): bool
    {
        // TODO: Implement email sending
        return false;
    }

    /**
     * Send SMS (placeholder)
     */
    protected function sendSms($users, string $title, string $body, array $data): bool
    {
        // TODO: Implement SMS sending
        return false;
    }
}
