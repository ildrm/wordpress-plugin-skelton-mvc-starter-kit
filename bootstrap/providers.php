<?php

declare(strict_types=1);

use StarterKit\Mvc\Providers\AppServiceProvider;
use StarterKit\Mvc\Providers\DatabaseServiceProvider;
use StarterKit\Mvc\Providers\EventServiceProvider;
use StarterKit\Mvc\Providers\ViewServiceProvider;
use StarterKit\Mvc\Providers\RoutingServiceProvider;
use StarterKit\Mvc\Providers\PluginServiceProvider;

return [
    AppServiceProvider::class,
    DatabaseServiceProvider::class,
    EventServiceProvider::class,
    ViewServiceProvider::class,
    RoutingServiceProvider::class,
    PluginServiceProvider::class,
];
