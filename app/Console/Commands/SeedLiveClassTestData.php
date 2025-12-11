<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\LiveClassTestDataSeeder;

class SeedLiveClassTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'live-class:seed-test-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed test data for live classes (course ID 1)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Seeding live class test data...');
        
        $seeder = new LiveClassTestDataSeeder();
        $seeder->run();
        
        $this->info('Live class test data seeded successfully!');
        $this->info('You can now test the APIs:');
        $this->info('- GET /api/v1/live-classes/upcoming');
        $this->info('- GET /api/v1/live-classes/recorded');
        $this->info('- GET /api/v1/courses/1/live-classes');
    }
}
