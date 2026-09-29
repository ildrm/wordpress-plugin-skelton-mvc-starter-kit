<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Console\Commands;

use StarterKit\Mvc\WordPress\Lifecycle\Upgrader;

final class MigrateCommand
{
    public function __construct(private Upgrader $upgrader)
    {
    }

    public function __invoke(): void
    {
        try {
            if (!$this->upgrader->maybeUpgrade(true)) {
                \WP_CLI::error('Another migration is running. Retry shortly.');
                return;
            }
            \WP_CLI::success('Plugin migrations are current.');
        } catch (\Throwable $exception) {
            \WP_CLI::error('Plugin migration failed: ' . $exception::class);
        }
    }
}
