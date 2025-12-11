<?php

namespace App\Http\Controllers\Api;

use App\Services\Notifications\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends BaseApiController
{
    protected NotificationService $service;

    public function __construct(NotificationService $service)
    {
        $this->service = $service;
    }

    /**
     * Get paginated notifications for authenticated user
     */
    public function index(Request $request)
    {
        $user = $this->getAuthUser();
        $perPage = $request->get('per_page', 10);
        
        $notifications = $this->service->getPaginatedNotificationsForUser($user->id, $perPage);
        
        // Mark loaded notifications as read after response is sent using register_shutdown_function
        $notificationIds = $notifications->pluck('id');
        register_shutdown_function(function () use ($notificationIds, $user) {
            $this->service->markNotificationsAsReadByIds($notificationIds, $user->id);
        });
        
        return $this->respondPaginated($notifications, 'Notifications fetched successfully');
    }
}
