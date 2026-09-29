<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Foundation;

use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionNamedType;
use StarterKit\Mvc\Contracts\Container\Container as ContainerContract;
use StarterKit\Mvc\Exceptions\BindingResolutionException;

final class Container implements ContainerContract
{
    /** @var array<string, array{concrete: string|callable, shared: bool}> */
    private array $bindings = [];
    /** @var array<string, object> */
    private array $instances = [];
    /** @var list<string> */
    private array $resolving = [];

    public function bind(string $abstract, string|callable|null $concrete = null): void
    {
        $this->bindings[$abstract] = ['concrete' => $concrete ?? $abstract, 'shared' => false];
        unset($this->instances[$abstract]);
    }

    public function singleton(string $abstract, string|callable|null $concrete = null): void
    {
        $this->bindings[$abstract] = ['concrete' => $concrete ?? $abstract, 'shared' => true];
        unset($this->instances[$abstract]);
    }

    public function instance(string $abstract, object $instance): void
    {
        $this->instances[$abstract] = $instance;
    }

    public function has(string $abstract): bool
    {
        return isset($this->instances[$abstract])
            || isset($this->bindings[$abstract])
            || class_exists($abstract);
    }

    public function get(string $abstract): mixed
    {
        return $this->make($abstract);
    }

    public function make(string $abstract, array $parameters = []): mixed
    {
        if ($parameters === [] && isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }
        if (in_array($abstract, $this->resolving, true)) {
            throw new BindingResolutionException('Circular dependency: ' . implode(' -> ', [...$this->resolving, $abstract]));
        }
        $this->resolving[] = $abstract;
        try {
            $binding = $this->bindings[$abstract] ?? ['concrete' => $abstract, 'shared' => false];
            $concrete = $binding['concrete'];
            $object = is_string($concrete) ? $this->build($concrete, $parameters) : $concrete($this, $parameters);
            if ($binding['shared'] && $parameters === [] && is_object($object)) {
                $this->instances[$abstract] = $object;
            }
            return $object;
        } finally {
            array_pop($this->resolving);
        }
    }

    private function build(string $class, array $parameters): object
    {
        if (!class_exists($class)) {
            throw new BindingResolutionException("Cannot resolve {$class}: class does not exist or interface is unbound.");
        }
        $reflection = new ReflectionClass($class);
        if (!$reflection->isInstantiable()) {
            throw new BindingResolutionException("Cannot instantiate {$class}.");
        }
        $constructor = $reflection->getConstructor();
        return $reflection->newInstanceArgs($constructor ? $this->resolveParameters($constructor->getParameters(), $parameters) : []);
    }

    public function call(callable|array $callback, array $parameters = []): mixed
    {
        $reflection = is_array($callback)
            ? new ReflectionMethod($callback[0], $callback[1])
            : (is_object($callback) && !$callback instanceof \Closure
                ? new ReflectionMethod($callback, '__invoke')
                : new ReflectionFunction($callback));
        return $callback(...$this->resolveParameters($reflection->getParameters(), $parameters));
    }

    private function resolveParameters(array $definitions, array $overrides): array
    {
        $arguments = [];
        foreach ($definitions as $parameter) {
            $name = $parameter->getName();
            if (array_key_exists($name, $overrides)) {
                $arguments[] = $overrides[$name];
                continue;
            }
            $type = $parameter->getType();
            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                $arguments[] = $this->make($type->getName());
            } elseif ($parameter->isDefaultValueAvailable()) {
                $arguments[] = $parameter->getDefaultValue();
            } else {
                throw new BindingResolutionException("Cannot resolve parameter \${$name} for {$parameter->getDeclaringClass()?->getName()}.");
            }
        }
        return $arguments;
    }
}
