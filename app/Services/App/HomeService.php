<?php

namespace App\Services\App;

use App\Services\App\AppBaseService;

use App\Services\Banners\BannerService;
use App\Services\Feeds\FeedService;
use App\Services\Testimonials\TestimonialService;
use App\Services\Reels\ReelService;
use App\Services\Galleries\GalleryImageService;
use App\Services\Notifications\NotificationService;
use App\Services\Users\ClientService;

class HomeService extends AppBaseService
{
    protected string $cachePrefix = 'home';
    protected int $defaultTtl = 300;

    public function __construct(
        protected BannerService $banners,
        protected FeedService $feeds,
        protected TestimonialService $testimonials,
        protected ReelService $reels,
        protected GalleryImageService $galleryImages,
        protected NotificationService $notifications,
        protected UserJourneyService $userJourneyService,
        protected ClientService $clientService,
    ) {}

    public function getHomeData(): array
    {
        $user = $this->getAuthUser();

        $sharedContent = $this->getSharedContent();
        $userSpecificContent = $this->getUserJourneyContent($user);
        $homeTools = $this->getHomeTools();
        $content = array_merge($sharedContent, $userSpecificContent, $homeTools);

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
    private function getUserJourneyContent($user)
    {
        
        return $this->remember("user:{$user->id}", function () use ($user) {
            $content = [];

            if($user->getMetaField('preparing_to_conceive') == 1) {
                $content['journey_cycle'] = $this->userJourneyService->calculatePeriodCycle($user->getMetaField('last_period_date'));
            }
            if($user->getMetaField('is_pregnant') == 1) {
                $content['journey_cycle'] = $this->userJourneyService->getPregnancyProgress();
            }
            if($user->getMetaField('is_delivered') == 1) {
                $content['journey_cycle'] = $this->userJourneyService->postpartumTimeline($user->getMetaField('baby_dob'));
            }
            $content['journey_cycle']['journey_type'] = $this->clientService->getJourneyStatus($user->id);
            
            return $content;
        });
    }

    private function getHomeTools(): array
    {
        return [
            'tools' => config('home_tools'),
        ];
    }
}
