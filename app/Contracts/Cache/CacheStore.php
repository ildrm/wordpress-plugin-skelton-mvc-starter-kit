<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Contracts\Cache;

interface CacheStore
{
    public function get(string $key, mixed $default = null): mixed;
    public function put(string $key, mixed $value, int $seconds): void;
    public function forget(string $key): void;
    public function remember(string $key, int $seconds, callable $callback): mixed;
}
