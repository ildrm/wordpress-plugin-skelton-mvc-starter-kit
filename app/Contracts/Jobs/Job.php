<?php

/**
 * Background job contract.
 *
 * @package StarterKit_Mvc
 */

declare(strict_types=1);

namespace StarterKit\Mvc\Contracts\Jobs;

/** Defines a container-resolved unit of work. */
interface Job
{
    /** Run one idempotent unit of work. */
    public function handle(): void;
}
