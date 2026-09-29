<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Providers;

use StarterKit\Mvc\Contracts\Database\Connection as ConnectionContract;
use StarterKit\Mvc\Database\Connection;
use StarterKit\Mvc\Foundation\ServiceProvider;

final class DatabaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ConnectionContract::class, Connection::class);
        $this->app->singleton(\wpdb::class, static function (): \wpdb {
            global $wpdb;
            return $wpdb;
        });
    }
}
