<?php

declare(strict_types=1);

namespace StarterKit\Mvc\WordPress\Blocks;

use StarterKit\Mvc\Foundation\Application;
use StarterKit\Mvc\WordPress\Shortcodes\GreetingShortcode;

final class GreetingBlock
{
    public function __construct(private Application $app, private GreetingShortcode $greeting)
    {
    }

    public function register(): void
    {
        wp_register_script(
            'starterkit-mvc-greeting-editor',
            $this->app->pluginUrl('resources/blocks/greeting/editor.js'),
            ['wp-blocks', 'wp-element', 'wp-i18n'],
            $this->app->version(),
            true
        );
        register_block_type($this->app->resourcePath('blocks/greeting'), [
            'editor_script' => 'starterkit-mvc-greeting-editor',
            'render_callback' => fn (): string => $this->greeting->render(),
        ]);
    }
}
