<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CourseMaterial;
use App\Models\Video;
use App\Models\Document;
use App\Models\Note;

class CleanupOrphanedMaterials extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'materials:cleanup-orphaned {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete course materials that have no associated content (video, document, audio, note, etc.)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        $this->info('Starting cleanup of orphaned materials...');
        
        // Get all materials
        $materials = CourseMaterial::all();
        $orphanedMaterials = [];
        
        foreach ($materials as $material) {
            $hasContent = false;
            
            // Check if material has any associated content
            switch ($material->type) {
                case 'video':
                    $hasContent = Video::where('material_id', $material->id)->exists();
                    break;
                    
                case 'document':
                    $hasContent = Document::where('material_id', $material->id)->exists();
                    break;
                    
                case 'text':
                case 'audio':
                    $hasContent = Note::where('material_id', $material->id)->exists();
                    break;
                    
                // Add other material types as needed
                case 'scorm':
                case 'live_class':
                case 'quiz':
                case 'exam':
                case 'assignment':
                case 'other':
                    // For these types, we might need to check other relationships
                    // For now, we'll consider them as having content if they exist
                    $hasContent = true;
                    break;
            }
            
            if (!$hasContent) {
                $orphanedMaterials[] = $material;
            }
        }
        
        if (empty($orphanedMaterials)) {
            $this->info('No orphaned materials found.');
            return;
        }
        
        $this->info('Found ' . count($orphanedMaterials) . ' orphaned materials:');
        
        // Display orphaned materials
        $headers = ['ID', 'Title', 'Type', 'Course ID', 'Unit ID', 'Status'];
        $rows = [];
        
        foreach ($orphanedMaterials as $material) {
            $rows[] = [
                $material->id,
                $material->title,
                $material->type,
                $material->course_id,
                $material->unit_id,
                $material->status
            ];
        }
        
        $this->table($headers, $rows);
        
        if ($isDryRun) {
            $this->info('DRY RUN: No materials were deleted. Use without --dry-run to actually delete.');
            return;
        }
        
        // Confirm deletion
        if (!$this->confirm('Are you sure you want to delete these ' . count($orphanedMaterials) . ' orphaned materials?')) {
            $this->info('Operation cancelled.');
            return;
        }
        
        // Delete orphaned materials
        $deletedCount = 0;
        foreach ($orphanedMaterials as $material) {
            try {
                $material->delete();
                $deletedCount++;
                $this->line("Deleted material: {$material->title} (ID: {$material->id})");
            } catch (\Exception $e) {
                $this->error("Failed to delete material {$material->id}: " . $e->getMessage());
            }
        }
        
        $this->info("Successfully deleted {$deletedCount} orphaned materials.");
    }
}