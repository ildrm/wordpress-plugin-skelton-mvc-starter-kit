<?php

declare(strict_types=1);

namespace StarterKit\Mvc\WordPress\Lifecycle;

use StarterKit\Mvc\Contracts\Repositories\SettingsRepository;
use StarterKit\Mvc\Database\Schema\Schema;
use StarterKit\Mvc\Foundation\Application;
use StarterKit\Mvc\WordPress\Cron\CronRegistrar;

final class Uninstaller
{
    public function __construct(private Application $app, private SettingsRepository $settings, private Schema $schema, private CronRegistrar $cron)
    {
    }

    public function uninstall(): void
    {
        $this->cron->unschedule();
        if (!$this->app->config()->get('plugin.delete_data_on_uninstall', false)) {
            return;
        }
        foreach ((array) $this->app->config()->get('database.tables', []) as $table) {
            $this->schema->drop($table);
        }
        $this->settings->delete();
        delete_option(Installer::VERSION_OPTION);
        delete_option('starterkit_mvc_plugin_version');
        delete_option('starterkit_mvc_migrations');
        delete_option('starterkit_mvc_upgrade_lock');
        delete_option('starterkit_mvc_upgrade_failed');
        delete_option('starterkit_mvc_upgrade_retry_at');
    }
}
