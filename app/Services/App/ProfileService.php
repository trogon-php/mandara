<?php

namespace App\Services\App;

use App\Services\App\AppBaseService;
use App\Services\Users\UserMetaService;
use App\Services\Users\UserService;
use App\Http\Resources\User\AppUserProfileResource;

class ProfileService extends AppBaseService
{
    protected string $cachePrefix = 'profile';
    protected int $defaultTtl = 300; // 5 minutes
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        $this->clearCache();
    }

    /**
     * Get the authenticated user's profile (cached)
     */
    public function getProfile(): array
    {
        $user = $this->getAuthUser();
        
        return $this->remember("user:{$user->id}", function () use ($user) {
            // Get user meta data
            $userMetaService = app(UserMetaService::class);
            $userMeta = $userMetaService->getUserMeta($user->id);
            
            // Create resource with meta data
            $resource = new AppUserProfileResource($user);
            $profileData = $resource->toArray(request());
            
            // Add meta data to profile
            $profileData['meta'] = $userMeta;
            
            return $profileData;
        });
    }

    /**
     * Update the authenticated user's profile
     */
    public function updateProfile(array $data): array
    {
        $user = $this->getAuthUser();
        
        $updatedUser = $this->userService->update($user->id, $data);
        
        if (!$updatedUser) {
            return [
                'status' => false,
                'message' => 'Profile update failed',
                'data' => null
            ];
        }

        // Clear profile cache for this user
        $this->forget("user:{$user->id}");
        
        // Clear user cache from IsUserActive middleware
        \Cache::forget("user:{$user->id}");
        
        // Update the global authUser instance with fresh data
        app()->instance('authUser', $updatedUser);

        // Get fresh profile data with proper resource transformation
        $profileData = $this->getProfile();
        
        return [
            'status' => true,
            'message' => 'Profile updated successfully',
            'data' => $profileData
        ];
    }
}
