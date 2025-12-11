<?php

namespace App\Services\Traits;

use App\Services\Videos\VimeoService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait HasVideoThumbnailProcessing
{
    protected VimeoService $vimeoService;

    public function __construct()
    {
        $this->vimeoService = app(VimeoService::class);
    }

    /**
     * Process thumbnail - download from Vimeo if no custom thumbnail provided
     * 
     * @param array $data The data array containing thumbnail information
     * @param string|null $vimeoVideoId The Vimeo video ID
     * @param object|null $existingRecord The existing record (for updates)
     * @param string $providerField The field name that contains the provider (default: 'provider')
     * @param string $providerIdField The field name that contains the provider ID (default: 'provider_id')
     * @return array The processed data array
     */
    protected function processVideoThumbnail(
        array $data, 
        ?string $vimeoVideoId = null, 
        ?object $existingRecord = null,
        string $providerField = 'provider',
        string $providerIdField = 'provider_id'
    ): array {
        // If custom thumbnail is provided (UploadedFile instance), use it
        if (!empty($data['thumbnail']) && $data['thumbnail'] instanceof \Illuminate\Http\UploadedFile) {
            return $data; // Keep custom thumbnail as-is, let file upload system handle it
        }
        
        // If existing thumbnail data is provided (from database), keep it
        if (!empty($data['thumbnail']) && is_array($data['thumbnail']) && (isset($data['thumbnail']['url']) || isset($data['thumbnail']['thumb']))) {
            return $data; // Keep existing thumbnail as-is
        }
        
        // If updating an existing record and it has a thumbnail, preserve it
        if ($existingRecord && $existingRecord->thumbnail && is_array($existingRecord->thumbnail)) {
            // Keep the existing thumbnail, don't download from Vimeo
            return $data;
        }
        
        // If no custom thumbnail and Vimeo video, download from Vimeo (only for new records or records without thumbnails)
        if ($vimeoVideoId && isset($data[$providerField]) && $data[$providerField] === 'vimeo') {
            $vimeoThumbnail = $this->vimeoService->getVideoThumbnail($vimeoVideoId);
            if ($vimeoThumbnail) {
                $uploadedFile = $this->downloadVimeoThumbnailAsUploadedFile($vimeoThumbnail, $vimeoVideoId);
                if ($uploadedFile) {
                    // Let the file upload system handle it
                    $data['thumbnail'] = $uploadedFile;
                }
            }
        }
        
        return $data;
    }

    /**
     * Download Vimeo thumbnail and create UploadedFile instance for file upload system
     * 
     * @param string $thumbnailUrl The Vimeo thumbnail URL
     * @param string $vimeoVideoId The Vimeo video ID
     * @return \Illuminate\Http\UploadedFile|null
     */
    protected function downloadVimeoThumbnailAsUploadedFile(string $thumbnailUrl, string $vimeoVideoId): ?\Illuminate\Http\UploadedFile
    {
        try {
            // Download the thumbnail
            $response = Http::timeout(30)->get($thumbnailUrl);
            if (!$response->successful()) {
                Log::error('Failed to download Vimeo thumbnail', [
                    'url' => $thumbnailUrl,
                    'status' => $response->status()
                ]);
                return null;
            }
            
            // Get image info
            $imageData = $response->body();
            $extension = pathinfo(parse_url($thumbnailUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            
            // Create temporary file
            $tempFile = tempnam(sys_get_temp_dir(), 'vimeo_thumb_');
            file_put_contents($tempFile, $imageData);
            
            // Create UploadedFile instance
            $uploadedFile = new \Illuminate\Http\UploadedFile(
                $tempFile,
                "vimeo_thumb_{$vimeoVideoId}.{$extension}",
                $response->header('Content-Type', 'image/jpeg'),
                null,
                true // test mode
            );
            
            Log::info('Vimeo thumbnail downloaded as UploadedFile', [
                'original_url' => $thumbnailUrl,
                'filename' => $uploadedFile->getClientOriginalName()
            ]);
            
            return $uploadedFile;
            
        } catch (\Exception $e) {
            Log::error('Exception downloading Vimeo thumbnail as UploadedFile', [
                'url' => $thumbnailUrl,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Extract Vimeo video ID from URL
     * 
     * @param string $url The Vimeo URL
     * @return string|null The video ID or null if not found
     */
    protected function extractVimeoVideoId(string $url): ?string
    {
        return $this->vimeoService->extractVideoId($url);
    }

    /**
     * Get Vimeo video info
     * 
     * @param string $videoId The Vimeo video ID
     * @return array|null The video info or null if not found
     */
    protected function getVimeoVideoInfo(string $videoId): ?array
    {
        return $this->vimeoService->getVideoInfo($videoId);
    }

    /**
     * Sync video links for a model
     * 
     * @param object $model The model instance
     * @param string $videoId The Vimeo video ID
     * @param string $videoableType The videoable type (e.g., 'demo_video', 'reel', 'feed')
     * @return void
     */
    protected function syncModelVideoLinks(object $model, string $videoId, string $videoableType = 'demo_video'): void
    {
        $this->vimeoService->syncVideoLinks($model, $videoId, $videoableType);
    }

    /**
     * Update video links for a model
     * 
     * @param object $model The model instance
     * @param string $videoId The Vimeo video ID
     * @param string $videoableType The videoable type (e.g., 'demo_video', 'reel', 'feed')
     * @return void
     */
    protected function updateModelVideoLinks(object $model, string $videoId, string $videoableType = 'demo_video'): void
    {
        $this->vimeoService->updateVideoLinks($model, $videoId, $videoableType);
    }

    /**
     * Check if video URL has changed
     * 
     * @param object $existingRecord The existing record
     * @param string $newVideoId The new video ID
     * @param string $providerIdField The field name that contains the provider ID
     * @return bool True if the URL has changed
     */
    protected function hasVideoUrlChanged(object $existingRecord, string $newVideoId, string $providerIdField = 'provider_id'): bool
    {
        $currentVideoId = $this->extractVimeoVideoId($existingRecord->{$providerIdField});
        return $currentVideoId !== $newVideoId;
    }

    /**
     * Check if video links exist for a model
     * 
     * @param int $modelId The model ID
     * @param string $videoableType The videoable type
     * @return int The count of existing video links
     */
    protected function getExistingVideoLinksCount(int $modelId, string $videoableType = 'demo_video'): int
    {
        return \App\Models\VideoLink::where('videoable_id', $modelId)
            ->where('videoable_type', $videoableType)
            ->count();
    }
}
