<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Enums\Role;
use Database\Seeders\StudentTestDataSeeder;

class RefreshStudentTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:refresh-test-data {--force : Force refresh without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh student test data by clearing existing students and adding new test data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('This will delete all existing students and add fresh test data. Continue?')) {
                $this->info('Operation cancelled.');
                return;
            }
        }

        $this->info('Clearing existing students...');
        
        // Delete all existing students (role_id = 2) but keep the original demo student
        $deletedCount = User::where('role_id', Role::STUDENT->value)
            ->where('name', '!=', 'Demo Student')
            ->delete();
        $this->info("Deleted {$deletedCount} existing students.");

        $this->info('Adding fresh student test data...');
        
        // Run the student test data seeder
        $this->call('db:seed', ['--class' => StudentTestDataSeeder::class]);

        $this->info('Student test data refreshed successfully!');
        
        // Show summary
        $studentCount = User::where('role_id', Role::STUDENT->value)->count();
        $this->info("Total students in database: {$studentCount}");
    }
}