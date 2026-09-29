<?php

declare(strict_types=1);

namespace StarterKit\Mvc\WordPress\Logging;

use StarterKit\Mvc\Contracts\Logging\Logger;

final class DebugLogger implements Logger
{
    public function error(\Throwable $exception): void
    {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            // Exception messages may contain input or secrets; log only the class.
            error_log('[starterkit-mvc] ' . $exception::class); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Explicit debug-only logger.
        }
    }
}
