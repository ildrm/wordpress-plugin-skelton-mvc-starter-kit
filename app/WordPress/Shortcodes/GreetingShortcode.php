<?php

declare(strict_types=1);

namespace StarterKit\Mvc\WordPress\Shortcodes;

use StarterKit\Mvc\Contracts\View\ViewFactory;
use StarterKit\Mvc\Services\GreetingService;
use StarterKit\Mvc\Support\Vite;

final class GreetingShortcode
{
    public function __construct(private GreetingService $greetings, private ViewFactory $views, private Vite $assets)
    {
    }

    public function render(): string
    {
        $greeting = $this->greetings->current();
        if ($greeting === null) {
            return '';
        }
        $this->assets->enqueue('resources/js/app.js', 'starterkit-mvc-frontend');
        return $this->views->make('frontend.greeting', ['greeting' => $greeting])->render();
    }
}
