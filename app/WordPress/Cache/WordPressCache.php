<?php

declare(strict_types=1);

namespace StarterKit\Mvc\WordPress\Cache;

use StarterKit\Mvc\Contracts\Cache\CacheStore;

final class WordPressCache implements CacheStore
{
    private const PREFIX = 'starterkit_mvc_';

    public function get(string $key, mixed $default = null): mixed
    {
        $stored = get_transient($this->key($key));
        return is_array($stored) && array_key_exists('value', $stored) ? $stored['value'] : $default;
    }

    public function put(string $key, mixed $value, int $seconds): void
    {
        set_transient($this->key($key), ['value' => $value], max(1, $seconds));
    }

    public function forget(string $key): void
    {
        delete_transient($this->key($key));
    }

    public function remember(string $key, int $seconds, callable $callback): mixed
    {
        $sentinel = new \stdClass();
        $value = $this->get($key, $sentinel);
        if ($value !== $sentinel) {
            return $value;
        }
        $value = $callback();
        $this->put($key, $value, $seconds);
        return $value;
    }

    private function key(string $key): string
    {
        return self::PREFIX . hash('sha256', $key);
    }
}
