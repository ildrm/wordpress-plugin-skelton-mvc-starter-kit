<?php

declare(strict_types=1);

namespace StarterKit\Mvc\WordPress\Lifecycle;

use StarterKit\Mvc\WordPress\Cron\CronRegistrar;

final class Deactivator
{
    public function __construct(private CronRegistrar $cron)
    {
    }

    public function deactivate(): void
    {
        $this->cron->unschedule();
    }
}
