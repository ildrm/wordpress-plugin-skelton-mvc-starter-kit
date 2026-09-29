<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Contracts\Container;

interface Container
{
    public function bind(string $abstract, string|callable|null $concrete = null): void;
    public function singleton(string $abstract, string|callable|null $concrete = null): void;
    public function instance(string $abstract, object $instance): void;
    public function make(string $abstract, array $parameters = []): mixed;
    public function call(callable|array $callback, array $parameters = []): mixed;
    public function has(string $abstract): bool;
}
