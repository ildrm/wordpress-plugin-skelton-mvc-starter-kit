<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Providers;

use StarterKit\Mvc\Contracts\Cache\CacheStore;
use StarterKit\Mvc\Contracts\Logging\Logger;
use StarterKit\Mvc\Contracts\Repositories\SettingsRepository;
use StarterKit\Mvc\Foundation\ServiceProvider;
use StarterKit\Mvc\Repositories\WordPress\OptionsSettingsRepository;
use StarterKit\Mvc\WordPress\Cache\WordPressCache;
use StarterKit\Mvc\WordPress\Logging\DebugLogger;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SettingsRepository::class, OptionsSettingsRepository::class);
        $this->app->singleton(CacheStore::class, WordPressCache::class);
        $this->app->singleton(Logger::class, DebugLogger::class);
    }
}
