<?php

namespace App\Services\App;

use App\Services\App\AppBaseService;

use App\Services\Banners\BannerService;
use App\Services\Feeds\FeedService;
use App\Services\Testimonials\TestimonialService;
use App\Services\Courses\CourseService;
use App\Services\Categories\CategoryService;
use App\Services\Reels\ReelService;
use App\Services\Galleries\GalleryImageService;
use App\Services\Notifications\NotificationService;

class HomeService extends AppBaseService
{
    protected string $cachePrefix = 'home';
    protected int $defaultTtl = 300;

    public function __construct(
        protected BannerService $banners,
        protected FeedService $feeds,
        protected TestimonialService $testimonials,
        protected CategoryService $categories,
        protected ReelService $reels,
        protected GalleryImageService $galleryImages,
        protected NotificationService $notifications,
        protected PregnancyService $pregnancyService,
    ) {}

    public function getHomeData(): array
    {
        $user = authUser();

        $sharedContent = $this->getSharedContent();
        $userSpecificContent = $this->getUserSpecificContent($user);
        $content = array_merge($sharedContent, $userSpecificContent);

        return $this->payload($content, 'Home dashboard fetched successfully');
    }

    /**
     * Get shared content for the home page
     */
    private function getSharedContent()
    {
        return $this->remember("shared", function () {
            return [
                'banners' => $this->banners->getActiveBanners(),
            ];
        });
    }

    /**
     * Get user specific content for the home page
     */
    private function getUserSpecificContent($user)
    {
        
        return $this->remember("user:{$user->id}", function () use ($user) {
            
            // Add pregnancy progress after banners
            $pregnancyProgress = $this->pregnancyService->getPregnancyProgress($user->id);
            if ($pregnancyProgress) {
                $content['pregnancy_progress'] = $pregnancyProgress;
            }
            
            return $content;
        });
    }
}
