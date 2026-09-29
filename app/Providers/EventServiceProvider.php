<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Providers;

use StarterKit\Mvc\Contracts\Events\Dispatcher;
use StarterKit\Mvc\Foundation\ServiceProvider;
use StarterKit\Mvc\Support\EventDispatcher;

final class EventServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Dispatcher::class, EventDispatcher::class);
    }

    public function boot(): void
    {
        $dispatcher = $this->app->make(Dispatcher::class);
        foreach ($this->app->config()->get('events.listeners', []) as $event => $listeners) {
            foreach ($listeners as $listener) {
                $dispatcher->listen($event, $listener);
            }
        }
    }
}
