<?php

namespace App\Services\Examples;

use App\Services\Core\BaseService;
use App\Services\Traits\HasVideoThumbnailProcessing;
use App\Models\Reel;

/**
 * Example service showing how to use HasVideoThumbnailProcessing trait
 * for other video-dependent modules like Reels, Feeds, etc.
 */
class ReelService extends BaseService
{
    use HasVideoThumbnailProcessing;
    
    protected string $modelClass = Reel::class;
    
    /**
     * Store a new reel with Vimeo integration
     */
    public function store(array $data): ?Reel
    {
        return \DB::transaction(function () use ($data) {
            // Handle Vimeo URL if provided
            if ($data['provider'] === 'vimeo' && !empty($data['video_url'])) {
                $vimeoVideoId = $this->extractVimeoVideoId($data['video_url']);
                
                if (!$vimeoVideoId) {
                    throw new \Exception('Invalid Vimeo URL provided');
                }
                
                // Get video info from Vimeo
                $videoInfo = $this->getVimeoVideoInfo($vimeoVideoId);
                if ($videoInfo) {
                    // Update title and description if not provided
                    if (empty($data['title'])) {
                        $data['title'] = $videoInfo['name'] ?? 'Untitled Reel';
                    }
                    if (empty($data['description'])) {
                        $data['description'] = $videoInfo['description'] ?? null;
                    }
                }
                
                // Process thumbnail (download from Vimeo if no custom thumbnail)
                $data = $this->processVideoThumbnail($data, $vimeoVideoId, null, 'provider', 'video_url');
            }
            
            // Create the reel
            $reel = parent::store($data);
            
            // Sync video links if it's a Vimeo video
            if ($data['provider'] === 'vimeo' && !empty($data['video_url'])) {
                $vimeoVideoId = $this->extractVimeoVideoId($data['video_url']);
                if ($vimeoVideoId) {
                    $this->syncModelVideoLinks($reel, $vimeoVideoId, 'reel');
                }
            }
            
            return $reel;
        });
    }
    
    /**
     * Update a reel with Vimeo integration
     */
    public function update(int $id, array $data): ?Reel
    {
        return \DB::transaction(function () use ($id, $data) {
            $reel = $this->find($id);
            if (!$reel) {
                return null;
            }
            
            // Handle Vimeo URL changes
            if ($data['provider'] === 'vimeo' && !empty($data['video_url'])) {
                $vimeoVideoId = $this->extractVimeoVideoId($data['video_url']);
                
                if (!$vimeoVideoId) {
                    throw new \Exception('Invalid Vimeo URL provided');
                }
                
                // Check if the video URL has actually changed
                $urlChanged = $this->hasVideoUrlChanged($reel, $vimeoVideoId, 'video_url');
                
                // Get video info from Vimeo
                $videoInfo = $this->getVimeoVideoInfo($vimeoVideoId);
                if ($videoInfo) {
                    // Update title and description if not provided
                    if (empty($data['title'])) {
                        $data['title'] = $videoInfo['name'] ?? 'Untitled Reel';
                    }
                    if (empty($data['description'])) {
                        $data['description'] = $videoInfo['description'] ?? null;
                    }
                }
                
                // Process thumbnail (download from Vimeo if no custom thumbnail)
                $data = $this->processVideoThumbnail($data, $vimeoVideoId, $reel, 'provider', 'video_url');
                
                // Update video links if URL changed OR if no video links exist
                $existingLinksCount = $this->getExistingVideoLinksCount($reel->id, 'reel');
                
                if ($urlChanged || $existingLinksCount === 0) {
                    $this->updateModelVideoLinks($reel, $vimeoVideoId, 'reel');
                }
            }
            
            // Update the reel
            return parent::update($id, $data);
        });
    }
    
    /**
     * Sync video links for a reel
     */
    public function syncVideoLinks(int $reelId, string $vimeoVideoId): bool
    {
        $reel = $this->find($reelId);
        if (!$reel) {
            return false;
        }
        
        return $this->vimeoService->syncVideoLinks($reel, $vimeoVideoId, 'reel');
    }
    
    /**
     * Get video links for a reel
     */
    public function getVideoLinks(int $reelId): \Illuminate\Support\Collection
    {
        $reel = $this->find($reelId, ['videoLinks']);
        return $reel ? $reel->videoLinks : collect();
    }
}
