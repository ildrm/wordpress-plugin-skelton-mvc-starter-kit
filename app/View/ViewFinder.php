<?php

declare(strict_types=1);

namespace StarterKit\Mvc\View;

use StarterKit\Mvc\Exceptions\ViewNotFoundException;
use StarterKit\Mvc\Foundation\Application;

final class ViewFinder
{
    public function __construct(private Application $app)
    {
    }

    public function find(string $name): string
    {
        if (!preg_match('/^[a-zA-Z0-9_-]+(?:\.[a-zA-Z0-9_-]+)*$/D', $name)) {
            throw new ViewNotFoundException('Invalid view name.');
        }
        $relative = str_replace('.', '/', $name) . '.php';
        if (str_starts_with($name, 'frontend.') && $this->app->config()->get('view.theme_overrides')) {
            $override = (string) $this->app->config()->get('view.override_directory') . '/' . substr($relative, strlen('frontend/'));
            $themeFile = locate_template($override, false, false);
            if ($themeFile !== '') {
                return $themeFile;
            }
        }
        $file = $this->app->resourcePath('views/' . $relative);
        if (!is_file($file)) {
            throw new ViewNotFoundException("View {$name} was not found.");
        }
        return $file;
    }
}
