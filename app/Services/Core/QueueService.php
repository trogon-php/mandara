<?php

namespace App\Services\Core;

use App\Jobs\BaseJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

class QueueService
{
    /**
     * Dispatch a job to the queue
     */
    public function dispatch(BaseJob $job, ?string $queue = null, ?int $delay = null): void
    {
        $queueName = $queue ?? $job->getQueueName();
        
        if ($delay) {
            $job->delay($delay);
        }
        
        $job->onQueue($queueName);
        dispatch($job);
        
        Log::info('Job dispatched', [
            'job' => get_class($job),
            'queue' => $queueName,
            'delay' => $delay,
        ]);
    }

    /**
     * Dispatch a job synchronously (for testing)
     */
    public function dispatchSync(BaseJob $job): void
    {
        $job->onConnection('sync');
        dispatch_sync($job);
    }

    /**
     * Dispatch multiple jobs in a batch
     */
    public function dispatchBatch(array $jobs, ?string $queue = null): void
    {
        $batch = Queue::batch($jobs);
        
        if ($queue) {
            $batch->onQueue($queue);
        }
        
        $batch->dispatch();
    }

    /**
     * Get queue statistics
     */
    public function getQueueStats(?string $queue = null): array
    {
        $queue = $queue ?? config('queue.default');
        
        // This would depend on your queue driver
        // For database driver, you can query the jobs table
        return [
            'pending' => \DB::table('jobs')->where('queue', $queue)->count(),
            'failed' => \DB::table('failed_jobs')->count(),
        ];
    }
}
