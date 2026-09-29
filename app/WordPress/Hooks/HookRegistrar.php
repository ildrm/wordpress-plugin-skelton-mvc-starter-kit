<?php

declare(strict_types=1);

namespace StarterKit\Mvc\WordPress\Hooks;

use StarterKit\Mvc\Foundation\Application;

final class HookRegistrar
{
    public function __construct(private Application $app)
    {
    }

    public function action(string $hook, array $handler, int $priority = 10, int $arguments = 1): void
    {
        add_action($hook, function (...$args) use ($handler): void {
            $this->invoke($handler, $args);
        }, $priority, $arguments);
    }

    public function filter(string $hook, array $handler, int $priority = 10, int $arguments = 1): void
    {
        add_filter($hook, fn (...$args) => $this->invoke($handler, $args), $priority, $arguments);
    }

    private function invoke(array $handler, array $args): mixed
    {
        $named = [];
        $object = $this->app->make($handler[0]);
        $method = new \ReflectionMethod($object, $handler[1]);
        foreach ($method->getParameters() as $index => $parameter) {
            if (array_key_exists($index, $args)) {
                $named[$parameter->getName()] = $args[$index];
            }
        }
        return $this->app->call([$object, $handler[1]], $named);
    }
}
