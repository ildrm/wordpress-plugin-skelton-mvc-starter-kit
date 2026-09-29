<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Routing;

use StarterKit\Mvc\Exceptions\ValidationException;
use StarterKit\Mvc\Contracts\Logging\Logger;
use StarterKit\Mvc\Foundation\Application;
use StarterKit\Mvc\Http\Requests\Request;
use StarterKit\Mvc\Http\Responses\RedirectResponse;
use StarterKit\Mvc\Http\Responses\ViewResponse;

final class AdminRouter
{
    private array $menus = [];
    private array $submenus = [];
    private RouteCollection $posts;

    public function __construct(private Application $app, private Logger $logger)
    {
        $this->posts = new RouteCollection();
    }

    public function menu(string $pageTitle, string $menuTitle, string $capability, string $slug, array $handler): void
    {
        $this->menus[] = compact('pageTitle', 'menuTitle', 'capability', 'slug', 'handler');
    }

    public function submenu(string $parentSlug, string $pageTitle, string $menuTitle, string $capability, string $slug, array $handler): void
    {
        $this->submenus[] = compact('parentSlug', 'pageTitle', 'menuTitle', 'capability', 'slug', 'handler');
    }

    public function post(string $action, array $handler): Route
    {
        return $this->posts->add(new Route('POST', $action, $handler));
    }

    public function register(): void
    {
        add_action('admin_menu', function (): void {
            foreach ($this->menus as $menu) {
                add_menu_page($menu['pageTitle'], $menu['menuTitle'], $menu['capability'], $menu['slug'], fn () => $this->render($menu), 'dashicons-admin-generic');
            }
            foreach ($this->submenus as $menu) {
                add_submenu_page($menu['parentSlug'], $menu['pageTitle'], $menu['menuTitle'], $menu['capability'], $menu['slug'], fn () => $this->render($menu));
            }
        });
        foreach ($this->posts->all() as $route) {
            add_action('admin_post_' . $route->path, fn () => $this->dispatchPost($route));
        }
    }

    private function render(array $menu): void
    {
        if (!current_user_can($menu['capability'])) {
            wp_die(esc_html__('Permission denied.', 'wordpress-mvc-starter'), '', ['response' => 403]);
        }
        try {
            $result = $this->app->call([$this->app->make($menu['handler'][0]), $menu['handler'][1]], ['request' => new Request()]);
            if (!$result instanceof ViewResponse) {
                throw new \LogicException('Admin pages must return ViewResponse.');
            }
            echo $result->render(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Templates escape by context.
        } catch (\Throwable $exception) {
            $this->logger->error($exception);
            wp_die(esc_html__('Unable to render this page.', 'wordpress-mvc-starter'), '', ['response' => 500]);
        }
    }

    private function dispatchPost(Route $route): void
    {
        if (!current_user_can($route->capability ?? 'manage_options')) {
            wp_die(esc_html__('Permission denied.', 'wordpress-mvc-starter'), '', ['response' => 403]);
        }
        check_admin_referer($route->nonce ?? $route->path);
        try {
            $class = $route->requestClass ?? Request::class;
            $result = $this->app->call([$this->app->make($route->handler[0]), $route->handler[1]], ['request' => $class::fromGlobals()]);
            if (!$result instanceof RedirectResponse) {
                throw new \LogicException('Admin form actions must return RedirectResponse.');
            }
            wp_safe_redirect($result->url);
            exit;
        } catch (ValidationException $exception) {
            wp_die(esc_html__('The supplied data is invalid.', 'wordpress-mvc-starter'), '', ['response' => 422]);
        } catch (\Throwable $exception) {
            $this->logger->error($exception);
            wp_die(esc_html__('Unable to save the form.', 'wordpress-mvc-starter'), '', ['response' => 500]);
        }
    }
}
