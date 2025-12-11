<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\User520LeaderboardSeeder;

class SeedUser520Data extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user520:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed leaderboard data for user 520';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Seeding leaderboard data for user 520...');
        
        $seeder = new User520LeaderboardSeeder();
        $seeder->run();
        
        $this->info('User 520 leaderboard data seeded successfully!');
    }
}
