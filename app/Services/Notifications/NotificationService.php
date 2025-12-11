<?php

namespace App\Services\Notifications;

use App\Services\Core\BaseService;
use App\Http\Resources\Notifications\AppNotificationCollection;
use App\Http\Resources\Notifications\AppNotificationResource;
use App\Models\Notification;
use App\Services\Courses\CourseService;
use Illuminate\Database\Eloquent\Model;

class NotificationService extends BaseService
{
    
    protected string $modelClass = Notification::class;

    public function __construct(private NotificationSenderService $notificationSenderService)
    {
        parent::__construct();
    }

    /**
     * Get filter configuration
     */
    public function getFilterConfig(): array
    {
        return [
            'premium' => [
                'type' => 'exact',
                'label' => 'Premium',
                'col' => 3,
                'options' => [
                    '1' => 'Premium',
                    '0' => 'Free',
                ],
            ],
            'course_id' => [
                'type' => 'exact',
                'label' => 'Course',
                'col' => 3,
                'options' => $this->getCoursesOptions(),
            ],
            'category_id' => [
                'type' => 'exact',
                'label' => 'Category',
                'col' => 3,
                'options' => $this->getCategoriesOptions(),
            ],
        ];
    }

    public function store(array $data): Model
    {
        // dd($data);
        // dd($freeEnrollments, $paidEnrollments);

        // prepare user ids for notification
        $course = app(CourseService::class)->find($data['course_id'],['enrollments']);

        if(isset($data['free']) && $data['free'] == 1) {
            $freeEnrollments = $course->enrollments->where('type', 'free')->pluck('user_id')->toArray();
        }
        if(isset($data['premium']) && $data['premium'] == 1) {
            $paidEnrollments = $course->enrollments->where('type', 'paid')->pluck('user_id')->toArray();
        }

        $userIds = array_merge($freeEnrollments ?? [], $paidEnrollments ?? []);

        if(!isset($data['free']) && !isset($data['premium'])) {
            $userIds = $course->enrollments->pluck('user_id')->toArray();
        }
        unset($data['free']);
        $notification = parent::store($data);
        // dd($userIds);
        $this->notificationSenderService->sendPushQueued($userIds, $notification->title, $notification->description);
        // dd("sented ... check job");

        return $notification;
    }

    /**
     * Get search fields configuration for UI
     */
    public function getSearchFieldsConfig(): array
    {
        return [
            'title' => 'Title',
            'description' => 'Description',
        ];
    }


    /**
     * Get default search fields
     */
    public function getDefaultSearchFields(): array
    {
        return ['title', 'description'];
    }

    /**
     * Get default sorting
     */
    public function getDefaultSorting(): array
    {
        return ['field' => 'created_at', 'direction' => 'desc'];
    }

    /**
     * Get courses options for dropdown
     */
    protected function getCoursesOptions(): array
    {
        return \App\Models\Course::pluck('title', 'id')->toArray();
    }

    /**
     * Get categories options for dropdown
     */
    protected function getCategoriesOptions(): array
    {
        return \App\Models\Category::pluck('title', 'id')->toArray();
    }

    /**
     * Get notifications for app
     */
    public function getAppNotifications(): array
    {
        $notifications = $this->model->sorted()->get();
        return (new AppNotificationCollection($notifications))->toArray(request());
    }

    /**
     * Get premium notifications
     */
    public function getPremiumNotifications(): array
    {
        $notifications = $this->model->where('premium', true)->sorted()->get();
        return (new AppNotificationCollection($notifications))->toArray(request());
    }

    /**
     * Get free notifications
     */
    public function getFreeNotifications(): array
    {
        $notifications = $this->model->where('premium', false)->sorted()->get();
        return (new AppNotificationCollection($notifications))->toArray(request());
    }

    /**
     * Get notifications by course
     */
    public function getNotificationsByCourse(int $courseId): array
    {
        $notifications = $this->model->where('course_id', $courseId)->sorted()->get();
        return (new AppNotificationCollection($notifications))->toArray(request());
    }

    /**
     * Get notifications by category
     */
    public function getNotificationsByCategory(int $categoryId): array
    {
        $notifications = $this->model->where('category_id', $categoryId)->sorted()->get();
        return (new AppNotificationCollection($notifications))->toArray(request());
    }

    /**
     * Get single notification for app
     */
    public function getAppNotification(int $id): array
    {
        $notification = $this->model->find($id);
        
        if (!$notification) {
            return [];
        }
        
        return (new AppNotificationResource($notification))->toArray(request());
    }

    /**
     * Get featured notifications
     */
    public function getFeaturedNotifications(int $limit = 5): array
    {
        $notifications = $this->model->where('featured', 1)
            ->sorted()
            ->limit($limit)
            ->get();
        return (new AppNotificationCollection($notifications))->toArray(request());
    }

    /**
     * Get recent notifications
     */
    public function getRecentNotifications(int $limit = 10): array
    {
        $notifications = $this->model->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
        return (new AppNotificationCollection($notifications))->toArray(request());
    }

    /**
     * Get notifications with relationships
     */
    public function getNotificationsWithRelations(): array
    {
        $notifications = $this->model->with(['course', 'category'])->sorted()->get();
        return (new AppNotificationCollection($notifications))->toArray(request());
    }

    /**
     * Get paginated notifications for a specific user
     */
    public function getPaginatedForUser(int $userId, int $perPage = 10)
    {
        return $this->model->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Mark all notifications as read for a specific user
     */
    public function markAllAsReadForUser(int $userId)
    {
        // Get all notification IDs
        $notificationIds = $this->model->pluck('id');
        
        // Mark all notifications as read for the user
        foreach ($notificationIds as $notificationId) {
            \App\Models\NotificationRead::updateOrCreate(
                [
                    'user_id' => $userId,
                    'notification_id' => $notificationId,
                ],
                [
                    'is_read' => true,
                    'read_at' => now(),
                ]
            );
        }
        
        return true;
    }

    /**
     * Get paginated notifications for user (without auto-read)
     */
    public function getPaginatedNotificationsForUser(int $userId, int $perPage = 10)
    {
        // Get user's enrollments with course_id and type
        $enrollments = \App\Models\Enrollment::where('user_id', $userId)
        ->where('status', 'active')
        ->get(['course_id', 'type']);

        // Separate free and paid enrolled course IDs
        $freeEnrolledCourseIds = $enrollments->where('type', 'free')->pluck('course_id')->toArray();
        $paidEnrolledCourseIds = $enrollments->where('type', 'paid')->pluck('course_id')->toArray();
        $allEnrolledCourseIds = $enrollments->pluck('course_id')->toArray();

        // Build query: notifications where user is enrolled AND matches enrollment type
        $notifications = $this->model
        ->where(function ($query) use ($freeEnrolledCourseIds, $paidEnrolledCourseIds, $allEnrolledCourseIds) {
            // Premium notifications - only for paid enrolled courses
            $query->where(function ($q) use ($paidEnrolledCourseIds) {
                $q->where('premium', 1)
                ->whereIn('course_id', $paidEnrolledCourseIds);
            })
            // Non-premium (free) notifications - for free enrolled courses
            ->orWhere(function ($q) use ($freeEnrolledCourseIds) {
                $q->where('premium', 0)
                ->whereIn('course_id', $freeEnrolledCourseIds);
            })
            // Notifications without premium flag (null) - for all enrolled courses
            ->orWhere(function ($q) use ($allEnrolledCourseIds) {
                $q->where('premium', null)
                ->whereIn('course_id', $allEnrolledCourseIds);
            });
        })
        // Eager load read status for this user
        ->with(['notificationReads' => function ($query) use ($userId) {
            $query->where('user_id', $userId);
        }])
        ->orderBy('created_at', 'desc')
        ->paginate($perPage);

        // dd($enrollments);
        // Get paginated notifications
        // $notifications = $this->getPaginatedForUser($userId, $perPage);
        
        // Transform notifications with current read status
        $notifications->getCollection()->transform(function ($notification) use ($userId) {
            return new \App\Http\Resources\Notifications\AppNotificationResource($notification, $userId);
        });
        
        return $notifications;
    }

    /**
     * Mark only the loaded notifications as read for a specific user
     */
    public function markLoadedNotificationsAsRead($notifications, int $userId)
    {
        // Get the notification IDs from the loaded notifications
        $notificationIds = $notifications->pluck('id');
        
        // Mark only these specific notifications as read for the user
        foreach ($notificationIds as $notificationId) {
            \App\Models\NotificationRead::updateOrCreate(
                [
                    'user_id' => $userId,
                    'notification_id' => $notificationId,
                ],
                [
                    'is_read' => true,
                    'read_at' => now(),
                ]
            );
        }
        
        return true;
    }

    /**
     * Mark specific notifications as read by their IDs
     */
    public function markNotificationsAsReadByIds($notificationIds, int $userId)
    {
        // Mark only these specific notifications as read for the user
        foreach ($notificationIds as $notificationId) {
            \App\Models\NotificationRead::updateOrCreate(
                [
                    'user_id' => $userId,
                    'notification_id' => $notificationId,
                ],
                [
                    'is_read' => true,
                    'read_at' => now(),
                ]
            );
        }
        
        return true;
    }

    /**
     * Get unread notifications count for user
     */
    public function getUnreadNotificationsCount(int $userId): int
    {
        // Get total notifications count
        $totalNotifications = $this->model->count();
        
        // Get read notifications count for this user
        $readNotifications = \App\Models\NotificationRead::where('user_id', $userId)
            ->where('is_read', true)
            ->count();
        
        // Unread notifications = Total notifications - Read notifications
        return $totalNotifications - $readNotifications;
    }
}
