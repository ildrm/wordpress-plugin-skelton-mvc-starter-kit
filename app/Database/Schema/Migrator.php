<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Database\Schema;

use StarterKit\Mvc\Foundation\Application;

final class Migrator
{
    public const OPTION = 'starterkit_mvc_migrations';

    public function __construct(private Application $app, private Schema $schema)
    {
    }

    public function run(): array
    {
        $applied = get_option(self::OPTION, []);
        $applied = is_array($applied) ? $applied : [];
        $completed = [];
        $files = glob($this->app->databasePath('migrations/*.php')) ?: [];
        sort($files, SORT_STRING);
        foreach ($files as $file) {
            $name = basename($file, '.php');
            if (in_array($name, $applied, true)) {
                continue;
            }
            $migration = require $file;
            if (!$migration instanceof Migration) {
                throw new \RuntimeException("Migration {$name} must implement Migration.");
            }
            $migration->up($this->schema);
            $applied[] = $name;
            update_option(self::OPTION, $applied, false);
            $completed[] = $name;
        }
        return $completed;
    }
}
