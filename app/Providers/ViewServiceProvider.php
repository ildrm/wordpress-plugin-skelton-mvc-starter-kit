<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Providers;

use StarterKit\Mvc\Contracts\View\ViewFactory as ViewFactoryContract;
use StarterKit\Mvc\Foundation\ServiceProvider;
use StarterKit\Mvc\View\ViewFactory;

final class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ViewFactoryContract::class, ViewFactory::class);
    }
}
