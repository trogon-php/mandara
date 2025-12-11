<?php

namespace App\Services\Media;

use App\Models\Media;
use App\Services\Core\BaseService;
use App\Services\Core\FileUploadService;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class MediaService extends BaseService
{
    protected $modelClass = Media::class;


    public function getSearchFieldsConfig(): array
    {
        return [
            'name' => 'text',
            'original_name' => 'text',
            'folder' => 'text',
        ];
    }

    public function getDefaultSorting(): array
    {
        return ['field' => 'id', 'direction' => 'desc'];
    }
    /**
     * Upload a file and create media record
     */
    public function uploadFile(UploadedFile $file, ?string $folder = null, ?int $uploadedBy = null): Media
    {
        // Determine file type
        $mimeType = $file->getMimeType();
        $fileType = $this->getFileTypeFromMime($mimeType);

        // Upload file using FileUploadService
        $filePath = FileUploadService::upload($file, $folder ?? 'media', null);

        // Get file dimensions if image
        $width = null;
        $height = null;
        if (str_starts_with($mimeType, 'image/')) {
            try {
                $manager = new ImageManager(new Driver());
                $image = $manager->read($file->getPathname());
                $width = $image->width();
                $height = $image->height();
            } catch (\Exception $e) {
                // Ignore if image processing fails
            }
        }

        // Create media record
        return $this->model->create([
            'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_type' => $fileType,
            'mime_type' => $mimeType,
            'file_size' => $file->getSize(),
            'width' => $width,
            'height' => $height,
            'folder' => $folder,
        ]);
    }

    /**
     * Upload multiple files
     */
    public function uploadFiles(array $files, ?string $folder = null, ?int $uploadedBy = null): array
    {
        $mediaItems = [];
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $mediaItems[] = $this->uploadFile($file, $folder, $uploadedBy);
            }
        }
        return $mediaItems;
    }

    /**
     * Get file URL by ID
     */
    public function getFileUrl(int $id): ?string
    {
        $media = $this->find($id);
        return $media ? file_url($media->file_path, $media->file_type) : null;
    }

    /**
     * Get file URL by path
     */
    public function getFileUrlByPath(string $path): ?string
    {
        return file_url($path);
    }

    /**
     * Determine file type from MIME type
     */
    protected function getFileTypeFromMime(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }
        if (str_starts_with($mimeType, 'video/')) {
            return 'video';
        }
        if (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        }
        if (in_array($mimeType, [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])) {
            return 'document';
        }
        return 'file';
    }

    /**
     * Get default search fields
     */
    public function getDefaultSearchFields(): array
    {
        return ['name', 'original_name', 'folder', 'alt_text'];
    }

    /**
     * Get filter configuration
     */
    public function getFilterConfig(): array
    {
        return [
            'file_type' => [
                'type' => 'select',
                'label' => 'File Type',
                'options' => [
                    'image' => 'Images',
                    'document' => 'Documents',
                    'video' => 'Videos',
                    'audio' => 'Audio',
                    'file' => 'Other Files',
                ],
            ],
            'folder' => [
                'type' => 'select',
                'label' => 'Folder',
                'options' => function() {
                    return $this->model->distinct('folder')
                        ->whereNotNull('folder')
                        ->pluck('folder', 'folder')
                        ->toArray();
                },
            ],
            'status' => [
                'type' => 'select',
                'label' => 'Status',
                'options' => [1 => 'Active', 0 => 'Inactive'],
            ],
        ];
    }

    /**
     * Override delete to handle file deletion
     */
    public function delete(int $id): bool
    {
        $media = $this->find($id);
        if (!$media) {
            return false;
        }

        // Delete physical file
        FileUploadService::delete($media->file_path);

        // Delete database record
        return parent::delete($id);
    }
}
