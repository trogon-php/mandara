<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\LeaderboardTestDataSeeder;

class SeedLeaderboardData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboard:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed leaderboard test data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Seeding leaderboard test data...');
        
        $seeder = new LeaderboardTestDataSeeder();
        $seeder->run();
        
        $this->info('Leaderboard test data seeded successfully!');
    }
}
