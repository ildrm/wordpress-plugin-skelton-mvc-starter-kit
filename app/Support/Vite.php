<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Support;

use StarterKit\Mvc\Foundation\Application;

final class Vite
{
    private ?array $manifest = null;

    public function __construct(private Application $app)
    {
    }

    public function enqueue(string $entry, string $handle): void
    {
        $server = $this->app->config()->get('assets.dev_server');
        if (is_string($server) && $server !== '' && $this->app->environment() === 'local') {
            wp_enqueue_script_module($handle, rtrim($server, '/') . '/' . ltrim($entry, '/'), [], $this->app->version());
            return;
        }
        $manifest = $this->manifest();
        if (!isset($manifest[$entry]['file'])) {
            return;
        }
        $record = $manifest[$entry];
        wp_enqueue_script_module($handle, $this->app->pluginUrl('public/build/' . $record['file']), [], $this->app->version());
        foreach ($record['css'] ?? [] as $index => $css) {
            wp_enqueue_style($handle . '-' . $index, $this->app->pluginUrl('public/build/' . $css), [], $this->app->version());
        }
    }

    private function manifest(): array
    {
        if ($this->manifest !== null) {
            return $this->manifest;
        }
        $file = $this->app->buildPath('manifest.json');
        if (!is_file($file)) {
            return $this->manifest = [];
        }
        $contents = json_decode((string) file_get_contents($file), true);
        return $this->manifest = is_array($contents) ? $contents : [];
    }
}
