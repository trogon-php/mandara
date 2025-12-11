<?php

namespace Database\Seeders;

use App\Models\PointsRule;
use App\Models\UserPointEvent;
use App\Models\UserDailyPoints;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class User520LeaderboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedUser520PointEvents();
        $this->seedUser520DailyPoints();
    }

    /**
     * Seed point events for user 520
     */
    private function seedUser520PointEvents(): void
    {
        $userId = 520;
        $activityTypes = ['video', 'exam', 'quiz', 'practice', 'assignment', 'referral', 'other'];
        
        // Clear existing events for user 520
        UserPointEvent::where('user_id', $userId)->delete();
        
        // Generate events for the last 30 days with high activity
        for ($i = 0; $i < 30; $i++) {
            $date = Carbon::now()->subDays($i);
            
            // User 520 is very active - 90% chance of activity each day
            if (rand(1, 100) <= 90) {
                // 3-10 events per day for this active user
                $eventCount = rand(3, 10);
                
                for ($j = 0; $j < $eventCount; $j++) {
                    $activityType = $activityTypes[array_rand($activityTypes)];
                    $points = PointsRule::where('activity_type', $activityType)->first()->points ?? 10;
                    
                    UserPointEvent::create([
                        'user_id' => $userId,
                        'activity_type' => $activityType,
                        'activity_id' => rand(1, 100),
                        'points' => $points,
                        'earned_at' => $date,
                    ]);
                }
            }
        }
    }

    /**
     * Seed daily points for user 520
     */
    private function seedUser520DailyPoints(): void
    {
        $userId = 520;
        
        // Clear existing daily points for user 520
        UserDailyPoints::where('user_id', $userId)->delete();
        
        // Generate daily points for the last 30 days
        for ($i = 0; $i < 30; $i++) {
            $date = Carbon::now()->subDays($i);
            
            // Get total points for this user on this date
            $totalPoints = UserPointEvent::where('user_id', $userId)
                ->whereDate('earned_at', $date)
                ->sum('points');
            
            if ($totalPoints > 0) {
                UserDailyPoints::create([
                    'user_id' => $userId,
                    'date' => $date,
                    'total_points' => $totalPoints,
                ]);
            }
        }
    }
}
