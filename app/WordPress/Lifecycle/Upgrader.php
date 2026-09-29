<?php

declare(strict_types=1);

namespace StarterKit\Mvc\WordPress\Lifecycle;

use StarterKit\Mvc\Foundation\Application;

final class Upgrader
{
    public function __construct(private Application $app, private Installer $installer, private MigrationLock $lock)
    {
    }

    public function maybeUpgrade(bool $force = false): bool
    {
        if (!$force && (int) get_option('starterkit_mvc_upgrade_retry_at', 0) > time()) {
            return false;
        }
        $installed = (string) get_option(Installer::VERSION_OPTION, '0');
        $target = (string) $this->app->config()->get('database.schema_version');
        if (version_compare($installed, $target, '>=')) {
            return true;
        }
        $token = $this->lock->acquire();
        if ($token === null) {
            return false;
        }
        try {
            $this->installer->install();
            delete_option('starterkit_mvc_upgrade_failed');
            delete_option('starterkit_mvc_upgrade_retry_at');
            return true;
        } catch (\Throwable $exception) {
            update_option('starterkit_mvc_upgrade_failed', 1, false);
            update_option('starterkit_mvc_upgrade_retry_at', time() + 300, false);
            throw $exception;
        } finally {
            $this->lock->release($token);
        }
    }
}
