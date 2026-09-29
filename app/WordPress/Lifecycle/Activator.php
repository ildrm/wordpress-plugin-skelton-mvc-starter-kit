<?php

declare(strict_types=1);

namespace StarterKit\Mvc\WordPress\Lifecycle;

use StarterKit\Mvc\WordPress\Cron\CronRegistrar;

final class Activator
{
    public function __construct(private Installer $installer, private CronRegistrar $cron)
    {
    }

    public function activate(bool $networkWide = false): void
    {
        if ($networkWide && is_multisite()) {
            wp_die(esc_html__('Network activation is not supported. Activate this plugin on each site.', 'wordpress-mvc-starter'));
        }
        $this->installer->install();
        $this->cron->schedule();
    }
}
