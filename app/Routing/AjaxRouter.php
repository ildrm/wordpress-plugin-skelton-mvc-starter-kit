<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Routing;

use StarterKit\Mvc\Exceptions\AuthorizationException;
use StarterKit\Mvc\Contracts\Logging\Logger;
use StarterKit\Mvc\Exceptions\ValidationException;
use StarterKit\Mvc\Foundation\Application;
use StarterKit\Mvc\Http\Requests\Request;
use StarterKit\Mvc\Http\Responses\JsonResponse;

final class AjaxRouter
{
    private RouteCollection $routes;

    public function __construct(private Application $app, private Logger $logger)
    {
        $this->routes = new RouteCollection();
    }

    public function post(string $action, array $handler): Route
    {
        return $this->routes->add(new Route('POST', $action, $handler));
    }

    public function register(): void
    {
        foreach ($this->routes->all() as $route) {
            $callback = function () use ($route): void {
                $this->dispatch($route);
            };
            add_action('wp_ajax_' . $route->path, $callback);
            if ($route->public) {
                add_action('wp_ajax_nopriv_' . $route->path, $callback);
            }
        }
    }

    private function dispatch(Route $route): void
    {
        $method = isset($_SERVER['REQUEST_METHOD']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_METHOD'])) : '';
        if ($method !== $route->method) {
            wp_send_json_error(['message' => __('Method not allowed.', 'wordpress-mvc-starter')], 405);
        }
        if (!$route->public && !is_user_logged_in()) {
            wp_send_json_error(['message' => __('Authentication required.', 'wordpress-mvc-starter')], 401);
        }
        if ($route->capability === null && !$route->public || $route->capability !== null && !current_user_can($route->capability)) {
            wp_send_json_error(['message' => __('Permission denied.', 'wordpress-mvc-starter')], 403);
        }
        $nonce = isset($_POST['_ajax_nonce']) ? sanitize_text_field(wp_unslash($_POST['_ajax_nonce'])) : '';
        if ($route->nonce === null || !wp_verify_nonce($nonce, $route->nonce)) {
            wp_send_json_error(['message' => __('Invalid security token.', 'wordpress-mvc-starter')], 403);
        }
        try {
            $class = $route->requestClass ?? Request::class;
            $result = $this->app->call([$this->app->make($route->handler[0]), $route->handler[1]], ['request' => $class::fromGlobals()]);
            wp_send_json_success($result instanceof JsonResponse ? $result->data : $result, $result instanceof JsonResponse ? $result->status : 200);
        } catch (ValidationException $exception) {
            wp_send_json_error(['message' => __('The supplied data is invalid.', 'wordpress-mvc-starter'), 'errors' => $exception->errors()], 422);
        } catch (AuthorizationException $exception) {
            wp_send_json_error(['message' => __('Permission denied.', 'wordpress-mvc-starter')], 403);
        } catch (\Throwable $exception) {
            $this->logger->error($exception);
            wp_send_json_error(['message' => __('An unexpected error occurred.', 'wordpress-mvc-starter')], 500);
        }
    }
}
