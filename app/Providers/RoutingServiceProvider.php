<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Providers;

use StarterKit\Mvc\Foundation\ServiceProvider;
use StarterKit\Mvc\Routing\AdminRouter;
use StarterKit\Mvc\Routing\AjaxRouter;
use StarterKit\Mvc\Routing\RestRouter;

final class RoutingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AdminRouter::class);
        $this->app->singleton(AjaxRouter::class);
        $this->app->singleton(RestRouter::class);
    }

    public function boot(): void
    {
        $rest = $this->app->make(RestRouter::class);
        (require $this->app->basePath('routes/api.php'))($rest);
        add_action('rest_api_init', [$rest, 'register']);

        if (is_admin()) {
            $admin = $this->app->make(AdminRouter::class);
            (require $this->app->basePath('routes/admin.php'))($admin);
            $admin->register();
            $ajax = $this->app->make(AjaxRouter::class);
            (require $this->app->basePath('routes/ajax.php'))($ajax);
            $ajax->register();
        }
    }
}
