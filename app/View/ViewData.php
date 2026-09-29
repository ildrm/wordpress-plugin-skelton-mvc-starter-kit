<?php

declare(strict_types=1);

namespace StarterKit\Mvc\View;

final readonly class ViewData
{
    public function __construct(private array $values, private ?\Closure $renderer = null)
    {
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->values[$key] ?? $default;
    }

    public function render(string $view, array $props = []): string
    {
        if ($this->renderer === null) {
            throw new \LogicException('Nested view rendering is unavailable.');
        }
        return ($this->renderer)($view, $props);
    }
}
