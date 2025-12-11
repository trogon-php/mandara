<?php

namespace App\Services\Core;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Encoders\GifEncoder;

class FileUploadService
{
    /**
     * Upload file(s) (supports images and non-images).
     *
     * - Non-images: stored directly
     * - Images: resized if preset provided
     * - Multiple files: returns array of paths/JSONs
     *
     * @param \Illuminate\Http\UploadedFile|\Illuminate\Http\UploadedFile[]|string $file
     * @param string $folder
     * @param string|null $presetKey
     * @param string|null $filename
     * @return string|array
     */
    public static function upload($file, $folder = 'uploads', $presetKey = null, $filename = null)
    {
        $disk = config('filesystems.default');
        $appFolder = config("filesystems.disks.{$disk}.app_folder", '');
        $folder = $appFolder ? "{$appFolder}/uploads/{$folder}" : "uploads/{$folder}";

        if (is_array($file)) {
            $results = [];
            foreach ($file as $singleFile) {
                $results[] = self::processSingleFile($singleFile, $folder, $presetKey, $filename);
            }
            return $results;
        }

        return self::processSingleFile($file, $folder, $presetKey, $filename);
    }

    /**
     * Process a single file (image or non-image).
     */
    protected static function processSingleFile($file, $folder, $presetKey = null, $filename = null)
    {
        $disk = config('filesystems.default');
        $appFolder = config("filesystems.disks.{$disk}.app_folder", '');

        $extension = is_string($file)
            ? pathinfo($file, PATHINFO_EXTENSION)
            : $file->getClientOriginalExtension();

        $filename = $filename ?? uniqid().'.'.$extension;

        // Normal file (no preset)
        if (!$presetKey) {
            $storagePath = "{$folder}/{$filename}";
            Storage::disk($disk)->putFileAs($folder, $file, $filename);
            
            // Return path without app folder
            $returnPath = $appFolder ? str_replace("{$appFolder}/", '', $storagePath) : $storagePath;
            return $returnPath;
        }

        // If image
        if (str_starts_with(mime_content_type($file->getPathname()), 'image/')) {
            $presets = (array) config('images.presets');
            $preset  = $presets[$presetKey] ?? null;

            if (!$preset) {
                throw new \InvalidArgumentException("Image preset '{$presetKey}' not found in config/images.php");
            }

            $quality = config("images.quality", 85);

            // Single-size preset [w,h]
            if (isset($preset[0]) && is_numeric($preset[0])) {
                $manager = new ImageManager(new Driver());
                $image = $manager->read($file)
                    ->resize($preset[0], $preset[1], function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->encode(new \Intervention\Image\Encoders\JpegEncoder(quality: $quality));

                $storagePath = "{$folder}/{$filename}";
                Storage::disk($disk)->put($storagePath, (string) $image);

                // Return path without app folder
                $returnPath = $appFolder ? str_replace("{$appFolder}/", '', $storagePath) : $storagePath;
                return $returnPath;
            }

            // Multi-size preset {thumb: [300,300], original: [1200,800]}
            $paths = [];
            foreach ($preset as $name => [$width, $height]) {
                $manager = new ImageManager(new Driver());
                $image = $manager->read($file)
                    ->resize($width, $height, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })
                    ->encode(match(strtolower($extension)) {
                        'jpg', 'jpeg' => new JpegEncoder(quality: $quality),
                        'png' => new PngEncoder(),
                        'webp' => new WebpEncoder(quality: $quality),
                        'gif' => new GifEncoder(),
                        default => new JpegEncoder(quality: $quality),
                    });

                $storagePath = "{$folder}/{$name}/{$filename}";
                Storage::disk($disk)->put($storagePath, (string) $image);

                // Return path without app folder
                $returnPath = $appFolder ? str_replace("{$appFolder}/", '', $storagePath) : $storagePath;
                $paths[$name] = $returnPath;
            }

            return $paths;
        }

        // Non-image but presetKey provided → treat as normal file
        $storagePath = "{$folder}/{$filename}";
        Storage::disk($disk)->putFileAs($folder, $file, $filename);
        
        // Return path without app folder
        $returnPath = $appFolder ? str_replace("{$appFolder}/", '', $storagePath) : $storagePath;
        return $returnPath;
    }

    public static function delete($paths): bool
    {
        
        if (!$paths) {
            return false;
        }

        $disk = config('filesystems.default');
        $appFolder = config("filesystems.disks.{$disk}.app_folder", '');
        


        // Normalize into array
        if (is_string($paths)) {
            $paths = [$paths];
        }

        $deleted = true;

        foreach ($paths as $key => $path) {
            // If value is array (multi-size preset), call delete recursively
            if (is_array($path)) {
                $deleted = $deleted && self::delete($path);
                continue;
            }

            // Add back app_folder if needed
            $storagePath = $appFolder ? "{$appFolder}/{$path}" : $path;

            if (Storage::disk($disk)->exists($storagePath)) {
                $deleted = $deleted && Storage::disk($disk)->delete($storagePath);
            }
        }

        return $deleted;
    }

    public static function copy(string $path, string $newName = null): ?string
    {
        $disk = config('filesystems.default');
        $appFolder = config("filesystems.disks.{$disk}.app_folder", '');

        $storagePath = $appFolder ? "{$appFolder}/{$path}" : $path;

        if (!Storage::disk($disk)->exists($storagePath)) {
            return null;
        }

        $folder = pathinfo($storagePath, PATHINFO_DIRNAME);
        $extension = pathinfo($storagePath, PATHINFO_EXTENSION);
        $filename = pathinfo($storagePath, PATHINFO_FILENAME);

        $newName = $newName ?? $filename . '-copy-' . uniqid() . '.' . $extension;
        $newPath = $folder . '/' . $newName;

        Storage::disk($disk)->copy($storagePath, $newPath);

        // Return relative path (without app_folder), like upload()
        return $appFolder ? str_replace("{$appFolder}/", '', $newPath) : $newPath;
    }

}



/**
 * Usage Examples
 * $course->banner = FileUploadService::upload($request->file('banner'), 'courses', 'banner');
 * $course->images = FileUploadService::upload($request->file('image'), 'courses', 'course');
 * $gallery = FileUploadService::upload($request->file('gallery'), 'courses/gallery', 'course');
 * returns an array of JSONs (multi-size per image)
 * $course->gallery = $gallery; 
 * */


