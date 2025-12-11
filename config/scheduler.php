<?php

return [
    'tasks' => [
        [
            'type' => 'command',
            'command' => 'activity-logs:cleanup',
            'frequency' => 'daily',
            'at' => '11:15',
            'without_overlapping' => true,
            'on_one_server' => true,
            // Optional: send output to file
            // 'send_output_to' => storage_path('logs/scheduler/activity-logs-cleanup.log'),
        ],
        // [
        //     'type' => 'call',
        //     'callable' => [\App\Services\Dashboard\DashboardService::class, 'clearCache'],
        //     'frequency' => 'hourly',
        // ],
        // Example: Scheduled job
        // [
        //     'type' => 'job',
        //     'job' => \App\Jobs\ProcessScheduledDataJob::class,
        //     'frequency' => 'daily',
        //     'at' => '03:00',
        // ],
        // Example: Custom cron expression
        // [
        //     'type' => 'command',
        //     'command' => 'custom:task',
        //     'frequency' => 'cron',
        //     'cron_expression' => '0 */6 * * *', // Every 6 hours
        // ],
    ],
];
