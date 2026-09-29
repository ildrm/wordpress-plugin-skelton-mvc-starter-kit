<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Contracts\Events;

interface Dispatcher
{
    public function listen(string $event, callable|string $listener): void;
    public function dispatch(object $event): void;
}
