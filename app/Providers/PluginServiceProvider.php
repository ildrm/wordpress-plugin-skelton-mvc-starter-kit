<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Providers;

use StarterKit\Mvc\Contracts\Logging\Logger;
use StarterKit\Mvc\Foundation\ServiceProvider;
use StarterKit\Mvc\Jobs\RefreshGreetingCache;
use StarterKit\Mvc\Support\Vite;
use StarterKit\Mvc\WordPress\Blocks\GreetingBlock;
use StarterKit\Mvc\WordPress\Cron\CronRegistrar;
use StarterKit\Mvc\WordPress\Lifecycle\Upgrader;
use StarterKit\Mvc\WordPress\Shortcodes\GreetingShortcode;

final class PluginServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CronRegistrar::class);
        $this->app->singleton(Vite::class);
        $this->app->make(CronRegistrar::class)->recurring(RefreshGreetingCache::class, 'hourly');
    }

    public function boot(): void
    {
        $cron = $this->app->make(CronRegistrar::class);
        $cron->register();
        add_action('init', function (): void {
            load_plugin_textdomain('wordpress-mvc-starter', false, dirname($this->app->pluginBasename()) . '/languages');
            try {
                $this->app->make(Upgrader::class)->maybeUpgrade();
            } catch (\Throwable $exception) {
                $this->app->make(Logger::class)->error($exception);
            }
            add_shortcode('starterkit_greeting', [$this->app->make(GreetingShortcode::class), 'render']);
            $this->app->make(GreetingBlock::class)->register();
        }, 5);
        if (is_admin()) {
            add_action('admin_notices', static function (): void {
                if (get_option('starterkit_mvc_upgrade_failed')) {
                    echo '<div class="notice notice-error"><p>'
                        . esc_html__('MVC Starter could not complete a database upgrade. Review the debug log and retry with WP-CLI.', 'wordpress-mvc-starter')
                        . '</p></div>';
                }
            });
            add_action('admin_enqueue_scripts', function (string $hook): void {
                if ($hook === 'toplevel_page_wordpress-mvc-starter') {
                    $this->app->make(Vite::class)->enqueue('resources/js/admin.js', 'starterkit-mvc-admin');
                }
            });
        }
        if (defined('WP_CLI') && WP_CLI) {
            foreach (require $this->app->basePath('routes/console.php') as $command => $class) {
                \WP_CLI::add_command($command, $this->app->make($class));
            }
        }
    }
}
