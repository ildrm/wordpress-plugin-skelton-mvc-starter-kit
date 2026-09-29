<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Console\Commands;

use StarterKit\Mvc\Foundation\Application;
use StarterKit\Mvc\WordPress\Lifecycle\Installer;

final class StatusCommand
{
    public function __construct(private Application $app)
    {
    }

    public function __invoke(): void
    {
        \WP_CLI::log('Plugin version: ' . $this->app->version());
        \WP_CLI::log('Schema version: ' . get_option(Installer::VERSION_OPTION, 'not installed'));
    }
}
