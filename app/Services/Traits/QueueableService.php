<?php

namespace App\Services\Traits;

use App\Jobs\BaseJob;
use App\Services\Core\QueueService;

trait QueueableService
{
    /**
     * Get the queue service instance
     */
    protected function getQueueService(): QueueService
    {
        return app(QueueService::class);
    }

    /**
     * Dispatch a job
     */
    protected function dispatchJob(BaseJob $job, ?string $queue = null, ?int $delay = null): void
    {
        $this->getQueueService()->dispatch($job, $queue, $delay);
    }

    /**
     * Dispatch a job synchronously (for testing)
     */
    protected function dispatchJobSync(BaseJob $job): void
    {
        $this->getQueueService()->dispatchSync($job);
    }
}
