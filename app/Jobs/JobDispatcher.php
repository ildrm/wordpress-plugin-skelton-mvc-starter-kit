<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Jobs;

use StarterKit\Mvc\WordPress\Cron\CronRegistrar;

final class JobDispatcher
{
    public function __construct(private CronRegistrar $cron)
    {
    }

    public function dispatchNow(string $jobClass): void
    {
        $this->cron->runNow($jobClass);
    }

    public function later(string $jobClass, int $timestamp): void
    {
        $this->cron->once($jobClass, $timestamp);
    }
}
