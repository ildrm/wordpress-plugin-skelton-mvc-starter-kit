<?php

declare(strict_types=1);

namespace StarterKit\Mvc\View;

use StarterKit\Mvc\Contracts\View\ViewFactory as ViewFactoryContract;

final class ViewFactory implements ViewFactoryContract
{
    /** @var array<string, list<callable>> */
    private array $composers = [];

    public function __construct(private ViewFinder $finder)
    {
    }

    public function composer(string $view, callable $callback): void
    {
        $this->composers[$view][] = $callback;
    }

    public function make(string $name, array $data = []): View
    {
        foreach ($this->composers[$name] ?? [] as $composer) {
            $data = array_merge($data, $composer($data));
        }
        return new View(
            $this->finder->find($name),
            new ViewData($data, fn (string $view, array $props): string => $this->make($view, $props)->render())
        );
    }
}
