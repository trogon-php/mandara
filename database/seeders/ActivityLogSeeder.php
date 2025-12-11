<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ActivityLog;

class ActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some sample activity logs for testing
        ActivityLog::create([
            'user_id' => 1, // Assuming user ID 1 exists
            'model_type' => 'App\Modules\Users\Models\User',
            'model_id' => 1,
            'action' => 'created',
            'changes' => null,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Test Browser)',
        ]);

        ActivityLog::create([
            'user_id' => 1,
            'model_type' => 'App\Modules\Users\Models\User',
            'model_id' => 1,
            'action' => 'updated',
            'changes' => json_encode(['name' => 'John Doe', 'email' => 'john@example.com']),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Test Browser)',
        ]);
    }
}
