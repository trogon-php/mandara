<?php

namespace App\Models;

use App\Models\BaseAuthModel;
use App\Models\Traits\AdminUser;
use App\Models\Traits\TutorUser;
use App\Models\Traits\StudentUser;

class User extends BaseAuthModel
{
    use AdminUser, TutorUser, StudentUser;

    protected $casts = [
        'name'               => 'string',
        'phone'              => 'string',
        'country_code'       => 'string',
        'email'              => 'string',
        'role_id'            => 'integer',
        'status'             => 'string',
        'profile_picture'    => 'string',
        'password'           => 'hashed',
    ];

    // $hidden = "don't show these fields when serializing".
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $fileFields = [
        'profile_picture' => [
            'single' => true,
            'folder' => 'users',
            'preset' => 'profile_picture',
        ]
    ];

    /**
     * Relationship with UserMeta
     */
    public function userMeta()
    {
        return $this->hasMany(UserMeta::class);
    }

    /**
     * Relationship with CourseTutor
     */
    public function courseTutors()
    {
        return $this->hasMany(CourseTutor::class);
    }

    /**
     * Get courses where user is a primary tutor
     */
    public function primaryTutorCourses()
    {
        return $this->hasMany(CourseTutor::class)->primary();
    }

    /**
     * Get courses where user is an assistant tutor
     */
    public function assistantTutorCourses()
    {
        return $this->hasMany(CourseTutor::class)->assistant();
    }

    /**
     * Find user by phone and country code
     */
    public static function findByPhone(string $phone, string $countryCode): ?User
    {
        return static::where('phone', $phone)
                    ->where('country_code', $countryCode)
                    ->first();
    }

    /**
     * Find user by email
     */
    public static function findByEmail(string $email): ?User
    {
        return static::where('email', $email)->first();
    }

    /**
     * Relationship with Wallet
     */
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    /**
     * Relationship with Referrals (as referrer)
     */
    public function referrals()
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    /**
     * Relationship with Referrals (as referred)
     */
    public function referredBy()
    {
        return $this->hasOne(Referral::class, 'referred_id');
    }

    /**
     * Create user with meta data
     */
    public static function createWithMeta(array $userData, array $metaData = []): User
    {
        // Create the user
        $user = static::create($userData);
        
        // Store meta fields if any
        if (!empty($metaData)) {
            $user->setMetaFields($metaData);
        }
        
        return $user;
    }

    /**
     * Get user meta field value
     */
    public function getMetaField(string $field, $default = null)
    {
        $meta = $this->userMeta()->where('meta_key', $field)->first();
        return $meta ? $meta->meta_value : $default;
    }

    /**
     * Set user meta field value
     */
    public function setMetaField(string $field, $value): void
    {
        $meta = $this->userMeta()->where('meta_key', $field)->first();
        
        if ($meta) {
            $meta->update(['meta_value' => $value]);
        } else {
            $this->userMeta()->create([
                'meta_key' => $field,
                'meta_value' => $value,
                'created_by' => $this->id,
            ]);
        }
    }

    /**
     * Get all meta fields as array
     */
    public function getMetaFields(): array
    {
        return $this->userMeta()->pluck('meta_value', 'meta_key')->toArray();
    }

    /**
     * Set multiple meta fields
     */
    public function setMetaFields(array $fields): void
    {
        foreach ($fields as $key => $value) {
            $this->setMetaField($key, $value);
        }
    }

    /**
     * Scope to get users by role
     */
    public function scopeByRole($query, int $roleId)
    {
        return $query->where('role_id', $roleId);
    }

    /**
     * Scope to get active users
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Relationship with NotificationRead
     */
    public function notificationReads()
    {
        return $this->hasMany(NotificationRead::class);
    }

    public function notifications()
    {
        return $this->belongsToMany(Notification::class, 'notification_reads')
                    ->withPivot(['is_read', 'read_at'])
                    ->withTimestamps();
    }

    // Helper methods for notifications
    public function markNotificationAsRead($notificationId)
    {
        return NotificationRead::updateOrCreate(
            [
                'user_id' => $this->id,
                'notification_id' => $notificationId,
            ],
            [
                'is_read' => true,
                'read_at' => now(),
            ]
        );
    }

    public function markNotificationAsUnread($notificationId)
    {
        return NotificationRead::updateOrCreate(
            [
                'user_id' => $this->id,
                'notification_id' => $notificationId,
            ],
            [
                'is_read' => false,
                'read_at' => null,
            ]
        );
    }

    public function getUnreadNotificationsCount()
    {
        return $this->notificationReads()->where('is_read', false)->count();
    }

    public function getReadNotificationsCount()
    {
        return $this->notificationReads()->where('is_read', true)->count();
    }

    /**
     * Get the coupons associated with this user
     */
    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'coupon_users', 'user_id', 'coupon_id')
            ->withTimestamps();
    }

    /**
     * Get the coupon usages for this user
     */
    public function couponUsages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Scope to get users by phone
     */
    public function scopeByPhone($query, string $phone, string $countryCode)
    {
        return $query->where('phone', $phone)->where('country_code', $countryCode);
    }

    /**
     * Override sorted scope since users table doesn't have sort_order column
     */
    public function scopeSorted($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
    /**
     * Relationship with UserDevice
     */
    public function devices()
    {
        return $this->hasMany(UserDevice::class);
    }

    /**
     * Get active devices
     */
    public function activeDevices()
    {
        return $this->devices()->where('is_active', true);
    }
}
