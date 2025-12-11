<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Seed student test data
        $this->call(StudentTestDataSeeder::class);
        
        // Seed wallet test data
        $this->call(WalletTestDataSeeder::class);
        
        // Seed notifications
        $this->call(NotificationSeeder::class);
        
        // Seed leaderboard test data
        $this->call(LeaderboardTestDataSeeder::class);
        
        // Seed exam data
        $this->call(ExamDataSeeder::class);
    }
}
