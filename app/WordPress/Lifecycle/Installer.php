<?php

declare(strict_types=1);

namespace StarterKit\Mvc\WordPress\Lifecycle;

use StarterKit\Mvc\Contracts\Repositories\SettingsRepository;
use StarterKit\Mvc\Database\Schema\Migrator;
use StarterKit\Mvc\Foundation\Application;

final class Installer
{
    public const VERSION_OPTION = 'starterkit_mvc_schema_version';

    public function __construct(private Application $app, private Migrator $migrator, private SettingsRepository $settings)
    {
    }

    public function install(): void
    {
        if (get_option('starterkit_mvc_settings', null) === null) {
            $this->settings->save($this->settings->get());
        }
        $this->migrator->run();
        update_option(self::VERSION_OPTION, $this->app->config()->get('database.schema_version'), false);
        update_option('starterkit_mvc_plugin_version', $this->app->version(), false);
        delete_option('starterkit_mvc_upgrade_failed');
        delete_option('starterkit_mvc_upgrade_retry_at');
    }
}
