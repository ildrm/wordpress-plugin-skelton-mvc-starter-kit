<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Jobs;

use StarterKit\Mvc\Contracts\Jobs\Job;
use StarterKit\Mvc\Services\GreetingService;

final class RefreshGreetingCache implements Job
{
    public function __construct(private GreetingService $greetings)
    {
    }

    public function handle(): void
    {
        $this->greetings->refresh();
    }
}
