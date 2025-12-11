<?php

namespace App\Services\Notifications;

use App\Models\User;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Collection;

class PushNotificationTemplateService
{
    /**
     * Base path for notification templates
     */
    protected const TEMPLATE_PATH = 'notifications.templates.push';

    /**
     * Render notification template for users
     * 
     * @param string $templateName Template name (e.g., 'live_class_created')
     * @param array|Collection|User|int $users User IDs, User models, or single user
     * @param array $variables Variables to pass to template
     * @return array Array of ['user_id' => ['title' => string, 'body' => string]]
     */
    public function render(string $templateName, $users, array $variables = []): array
    {
        $userIds = $this->getUserIds($users);
        
        if (empty($userIds)) {
            return [];
        }

        // Load all users at once
        $userModels = User::whereIn('id', $userIds)->get()->keyBy('id');
        
        $results = [];
        $viewPath = self::TEMPLATE_PATH . '.' . $templateName;

        foreach ($userIds as $userId) {
            $user = $userModels->get($userId);
            
            if (!$user) {
                continue;
            }

            // Merge user data with provided variables
            $data = array_merge([
                'user' => $user,
                'user_name' => $user->name ?? '',
                'user_email' => $user->email ?? '',
            ], $variables);

            // Check if variables are per-user
            if (isset($variables[$userId]) && is_array($variables[$userId])) {
                $data = array_merge($data, $variables[$userId]);
            }

            try {
                $results[$userId] = [
                    'user_id' => $userId,
                    'title' => trim(View::make($viewPath . '.title', $data)->render()),
                    'body' => trim(View::make($viewPath . '.body', $data)->render()),
                ];
            } catch (\Exception $e) {
                \Log::error('Failed to render notification template', [
                    'template' => $templateName,
                    'user_id' => $userId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $results;
    }

    /**
     * Extract user IDs from various input types
     */
    protected function getUserIds($users): array
    {
        if (is_array($users)) {
            if (empty($users)) {
                return [];
            }
            $first = $users[0];
            if (is_numeric($first)) {
                return $users;
            }
            if ($first instanceof User) {
                return array_column($users, 'id');
            }
        }

        if ($users instanceof Collection) {
            return $users->pluck('id')->toArray();
        }

        if ($users instanceof User) {
            return [$users->id];
        }

        if (is_numeric($users)) {
            return [$users];
        }

        return [];
    }
}
