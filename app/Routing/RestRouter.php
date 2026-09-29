<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Routing;

use StarterKit\Mvc\Exceptions\AuthorizationException;
use StarterKit\Mvc\Exceptions\ValidationException;
use StarterKit\Mvc\Contracts\Logging\Logger;
use StarterKit\Mvc\Foundation\Application;
use StarterKit\Mvc\Http\Requests\Request;
use StarterKit\Mvc\Http\Responses\JsonResponse;

final class RestRouter
{
    private RouteCollection $routes;

    public function __construct(private Application $app, private Logger $logger)
    {
        $this->routes = new RouteCollection();
    }

    public function get(string $path, array $handler): Route
    {
        return $this->routes->add(new Route('GET', $path, $handler));
    }
    public function post(string $path, array $handler): Route
    {
        return $this->routes->add(new Route('POST', $path, $handler));
    }
    public function put(string $path, array $handler): Route
    {
        return $this->routes->add(new Route('PUT', $path, $handler));
    }
    public function patch(string $path, array $handler): Route
    {
        return $this->routes->add(new Route('PATCH', $path, $handler));
    }
    public function delete(string $path, array $handler): Route
    {
        return $this->routes->add(new Route('DELETE', $path, $handler));
    }

    public function register(): void
    {
        foreach ($this->routes->all() as $route) {
            register_rest_route((string) $this->app->config()->get('routes.rest_namespace'), $route->path, [
                'methods' => $route->method,
                'args' => $route->args,
                'permission_callback' => function (\WP_REST_Request $request) use ($route): bool|\WP_Error {
                    if ($route->public) {
                        return true;
                    }
                    $allowed = $route->capability !== null && current_user_can($route->capability);
                    if ($allowed && $route->policy !== null) {
                        try {
                            $allowed = (bool) $this->app->call(
                                [$this->app->make($route->policy[0]), $route->policy[1]],
                                ['user' => wp_get_current_user(), 'request' => $request]
                            );
                        } catch (\Throwable $exception) {
                            $this->logger->error($exception);
                            return new \WP_Error('starterkit_error', __('An unexpected error occurred.', 'wordpress-mvc-starter'), ['status' => 500]);
                        }
                    }
                    if ($allowed) {
                        return true;
                    }
                    return new \WP_Error('starterkit_forbidden', __('You are not allowed to access this endpoint.', 'wordpress-mvc-starter'), ['status' => is_user_logged_in() ? 403 : 401]);
                },
                'callback' => fn (\WP_REST_Request $request): mixed => $this->dispatch($route, $request),
            ]);
        }
    }

    private function dispatch(Route $route, \WP_REST_Request $request): mixed
    {
        try {
            $class = $route->requestClass ?? Request::class;
            $input = $class::fromRest($request);
            $result = $this->app->call([$this->app->make($route->handler[0]), $route->handler[1]], ['request' => $input]);
            return $result instanceof JsonResponse ? $result->toRest() : rest_ensure_response($result);
        } catch (ValidationException $exception) {
            return new \WP_Error('starterkit_validation', __('The supplied data is invalid.', 'wordpress-mvc-starter'), ['status' => 422, 'errors' => $exception->errors()]);
        } catch (AuthorizationException $exception) {
            return new \WP_Error('starterkit_forbidden', __('Permission denied.', 'wordpress-mvc-starter'), ['status' => 403]);
        } catch (\Throwable $exception) {
            $this->logger->error($exception);
            return new \WP_Error('starterkit_error', __('An unexpected error occurred.', 'wordpress-mvc-starter'), ['status' => 500]);
        }
    }
}
