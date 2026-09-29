<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Foundation\Configuration;

use StarterKit\Mvc\Exceptions\ConfigurationException;

final class Repository
{
    public function __construct(private array $items = [])
    {
    }

    public function load(string $directory): void
    {
        foreach (glob($directory . '/*.php') ?: [] as $file) {
            $value = require $file;
            if (!is_array($value)) {
                throw new ConfigurationException("Configuration {$file} must return an array.");
            }
            $this->items[basename($file, '.php')] = $value;
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $value = $this->items;
        foreach (explode('.', $key) as $part) {
            if (!is_array($value) || !array_key_exists($part, $value)) {
                return $default;
            }
            $value = $value[$part];
        }
        return $value;
    }

    public function set(string $key, mixed $value): void
    {
        $parts = explode('.', $key);
        $target = &$this->items;
        foreach ($parts as $part) {
            $target[$part] ??= [];
            $target = &$target[$part];
        }
        $target = $value;
    }
}
