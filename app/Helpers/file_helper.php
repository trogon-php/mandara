<?php
use Illuminate\Support\Facades\Storage;

if (! function_exists('file_url')) {
    /**
     * Generate a public (or signed) URL for a file.
     */
    function file_url($path, $type = null, $expire = null)
    {
        // Handle array input (should not happen, but prevent errors)
        if (is_array($path)) {
            return null;
        }
        
        // If path is already a URL, return it as-is
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }
           
        $disk = config('filesystems.default');
        $appFolder = config("filesystems.disks.{$disk}.app_folder", '');

        $path = $appFolder ? $appFolder.'/'.$path : $path;

        if ($path) {
            // Signed URL for private buckets
            if ($expire && method_exists(Storage::disk($disk), 'temporaryUrl')) {
                return Storage::disk($disk)->temporaryUrl($path, now()->addMinutes($expire));
            }

            return Storage::disk($disk)->url($path);
        }

        // If no file and type fallback exists (for views/UI)
        if ($type && config("images.fallbacks.{$type}")) {
            return asset(config("images.fallbacks.{$type}"));
        }

        // Default: return null (useful for APIs)
        return null;
    }
}


if (! function_exists('getImagePreset')) {
    /**
     * Get image preset (size + fallback) from config/images.php
     *
     * Usage:
     *  getImagePreset('profile')
     *  getImagePreset('course', 'thumb')
     *  getImagePreset('course.thumb')         // dot notation
     *
     * @param string $preset
     * @param ?string $subPreset  For multi-size e.g., 'thumb' | can be omitted if using dot notation
     * @return array|null [
     *   'width' => int,
     *   'height'=> int,
     *   'fallback' => string|null
     * ]
     */
    function getImagePreset(string $preset, ?string $subPreset = null): ?array
    {
        // Support dot notation: 'course.thumb'
        if (str_contains($preset, '.')) {
            [$preset, $sub] = explode('.', $preset, 2);
            $subPreset = $subPreset ?? $sub;
        }

        $presets  = config("images.presets.$preset");
        $fallback = config("images.fallbacks.$preset") ?? null;

        if (! $presets) {
            return null;
        }

        // Multi-size preset (associative array like ['original'=>[w,h], 'thumb'=>[w,h], ...])
        if (is_array($presets) && array_keys($presets) !== range(0, count($presets) - 1)) {
            if (! $subPreset || ! isset($presets[$subPreset])) {
                return null; // sub-size not specified or not found
            }
            return [
                'width'    => (int) ($presets[$subPreset][0] ?? 0),
                'height'   => (int) ($presets[$subPreset][1] ?? 0),
                'fallback' => $fallback,
            ];
        }

        // Single-size preset (numeric array like [w, h])
        return [
            'width'    => (int) ($presets[0] ?? 0),
            'height'   => (int) ($presets[1] ?? 0),
            'fallback' => $fallback,
        ];
    }
}




/**
 * Profile image with fallback
 * <img src="{{ file_url($user->avatar, 'images/default-avatar.png') }}">
 * 
 * Course thumbnail (multi-size images in JSON)
 * <img src="{{ file_url($course->images['thumb'], 'images/course-placeholder.png') }}">
 * 
 * Private course PDF with signed URL
 * <a href="{{ file_url($course->pdf_path, null, 30) }}" target="_blank">Download PDF</a>
 * 
 * <!-- User profile with fallback -->
 * <img src="{{ file_url($user->avatar, 'profile') }}" alt="Profile">
 * 
 * <!-- Course thumbnail with fallback -->
 * <img src="{{ file_url($course->images['thumb'], 'course') }}" alt="Course Thumb">
 */