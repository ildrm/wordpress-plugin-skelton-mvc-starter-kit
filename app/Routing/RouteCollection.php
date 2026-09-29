<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Routing;

final class RouteCollection
{
    /** @var list<Route> */
    private array $routes = [];

    public function add(Route $route): Route
    {
        foreach ($this->routes as $existing) {
            if ($existing->method === $route->method && $existing->path === $route->path) {
                throw new \LogicException("Duplicate route {$route->method} {$route->path}.");
            }
        }
        $this->routes[] = $route;
        return $route;
    }

    public function all(): array
    {
        return $this->routes;
    }
}
