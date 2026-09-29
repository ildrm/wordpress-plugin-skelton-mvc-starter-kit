<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Contracts\Logging;

interface Logger
{
    public function error(\Throwable $exception): void;
}
