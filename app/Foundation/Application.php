<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Foundation;

use StarterKit\Mvc\Contracts\Container\Container as ContainerContract;
use StarterKit\Mvc\Foundation\Configuration\Repository;

final class Application implements ContainerContract
{
    private Container $container;
    private Repository $config;
    /** @var list<ServiceProvider> */
    private array $providers = [];
    private bool $booted = false;

    public function __construct(private string $basePath, private string $pluginFile, private string $version)
    {
        $this->container = new Container();
        $this->config = new Repository();
        $this->instance(self::class, $this);
        $this->instance(ContainerContract::class, $this);
        $this->instance(Repository::class, $this->config);
    }

    public function basePath(string $path = ''): string
    {
        return $this->path($this->basePath, $path);
    }
    public function appPath(string $path = ''): string
    {
        return $this->basePath('app/' . $path);
    }
    public function configPath(string $path = ''): string
    {
        return $this->basePath('config/' . $path);
    }
    public function resourcePath(string $path = ''): string
    {
        return $this->basePath('resources/' . $path);
    }
    public function databasePath(string $path = ''): string
    {
        return $this->basePath('database/' . $path);
    }
    public function buildPath(string $path = ''): string
    {
        return $this->basePath('public/build/' . $path);
    }
    public function pluginFile(): string
    {
        return $this->pluginFile;
    }
    public function pluginBasename(): string
    {
        return plugin_basename($this->pluginFile);
    }
    public function pluginUrl(string $path = ''): string
    {
        return plugins_url(ltrim($path, '/'), $this->pluginFile);
    }
    public function version(): string
    {
        return $this->version;
    }
    public function environment(): string
    {
        return function_exists('wp_get_environment_type') ? wp_get_environment_type() : 'production';
    }
    public function config(): Repository
    {
        return $this->config;
    }
    public function loadConfiguration(): void
    {
        $this->config->load($this->configPath());
    }
    public function bind(string $abstract, string|callable|null $concrete = null): void
    {
        $this->container->bind($abstract, $concrete);
    }
    public function singleton(string $abstract, string|callable|null $concrete = null): void
    {
        $this->container->singleton($abstract, $concrete);
    }
    public function instance(string $abstract, object $instance): void
    {
        $this->container->instance($abstract, $instance);
    }
    public function make(string $abstract, array $parameters = []): mixed
    {
        return $this->container->make($abstract, $parameters);
    }
    public function call(callable|array $callback, array $parameters = []): mixed
    {
        return $this->container->call($callback, $parameters);
    }
    public function has(string $abstract): bool
    {
        return $this->container->has($abstract);
    }
    public function get(string $abstract): mixed
    {
        return $this->make($abstract);
    }

    public function register(string $providerClass): void
    {
        if ($this->booted) {
            throw new \LogicException('Providers cannot be registered after boot.');
        }
        /** @var ServiceProvider $provider */
        $provider = new $providerClass($this);
        $provider->register();
        $this->providers[] = $provider;
    }

    public function boot(): void
    {
        if ($this->booted) {
            return;
        }
        foreach ($this->providers as $provider) {
            $provider->boot();
        }
        $this->booted = true;
    }

    public function isBooted(): bool
    {
        return $this->booted;
    }

    private function path(string $base, string $path): string
    {
        return rtrim($base, '/') . ($path === '' ? '' : '/' . ltrim($path, '/'));
    }
}
