<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Support;

use StarterKit\Mvc\Contracts\Events\Dispatcher;
use StarterKit\Mvc\Foundation\Application;

final class EventDispatcher implements Dispatcher
{
    private array $listeners = [];

    public function __construct(private Application $app)
    {
    }

    public function listen(string $event, callable|string $listener): void
    {
        $this->listeners[$event][] = $listener;
    }

    public function dispatch(object $event): void
    {
        foreach ($this->listeners[$event::class] ?? [] as $listener) {
            if (is_string($listener)) {
                $listener = $this->app->make($listener);
            }
            $this->app->call($listener, ['event' => $event]);
        }
    }
}
