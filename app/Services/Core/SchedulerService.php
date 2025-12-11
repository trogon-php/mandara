<?php

namespace App\Services\Core;

use Illuminate\Console\Scheduling\Schedule;

class SchedulerService
{
    /**
     * Register scheduled tasks
     */
    public function register(Schedule $schedule): void
    {
        // Register tasks from config file
        $this->registerFromConfig($schedule);
    }

    /**
     * Register tasks from config file
     */
    protected function registerFromConfig(Schedule $schedule): void
    {
        $tasks = config('scheduler.tasks', []);

        foreach ($tasks as $task) {
            $this->registerTask($schedule, $task);
        }
    }

    /**
     * Register a single task
     */
    protected function registerTask(Schedule $schedule, array $task): void
    {
        $method = $task['type'] ?? 'command'; // command, call, job
        
        switch ($method) {
            case 'command':
                $command = $schedule->command($task['command']);
                break;
            case 'call':
                // Handle callable - can be array or closure
                $callable = $task['callable'];
                if (is_array($callable) && count($callable) === 2) {
                    // [Class::class, 'method'] format
                    $command = $schedule->call(function () use ($callable) {
                        $class = is_string($callable[0]) ? app($callable[0]) : $callable[0];
                        call_user_func([$class, $callable[1]]);
                    });
                } else {
                    $command = $schedule->call($callable);
                }
                break;
            case 'job':
                $jobClass = $task['job'];
                $job = is_string($jobClass) ? new $jobClass() : $jobClass;
                $command = $schedule->job($job);
                break;
            default:
                return;
        }

        // Apply frequency
        $this->applyFrequency($command, $task['frequency'] ?? 'daily');
        
        // Apply options
        if ($task['without_overlapping'] ?? false) {
            $command->withoutOverlapping();
        }
        
        if ($task['on_one_server'] ?? false) {
            $command->onOneServer();
        }
        
        if (isset($task['at'])) {
            $command->at($task['at']);
        }

        // Add email output on failure (optional)
        if (isset($task['email_on_failure'])) {
            $command->emailOutputOnFailure($task['email_on_failure']);
        }

        // Add output to file (optional)
        if (isset($task['send_output_to'])) {
            $command->sendOutputTo($task['send_output_to']);
        }
    }

    /**
     * Apply frequency to schedule command
     */
    protected function applyFrequency($command, string $frequency): void
    {
        match ($frequency) {
            'everyMinute' => $command->everyMinute(),
            'everyTwoMinutes' => $command->everyTwoMinutes(),
            'everyFiveMinutes' => $command->everyFiveMinutes(),
            'everyTenMinutes' => $command->everyTenMinutes(),
            'everyFifteenMinutes' => $command->everyFifteenMinutes(),
            'everyThirtyMinutes' => $command->everyThirtyMinutes(),
            'hourly' => $command->hourly(),
            'daily' => $command->daily(),
            'weekly' => $command->weekly(),
            'monthly' => $command->monthly(),
            'yearly' => $command->yearly(),
            'cron' => $command->cron($task['cron_expression'] ?? '* * * * *'),
            default => $command->daily(),
        };
    }
}
