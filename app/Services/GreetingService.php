<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Services;

use StarterKit\Mvc\Contracts\Cache\CacheStore;
use StarterKit\Mvc\Contracts\Repositories\SettingsRepository;

final class GreetingService
{
    public function __construct(private SettingsRepository $settings, private CacheStore $cache)
    {
    }

    public function current(): ?string
    {
        return $this->cache->remember('greeting', HOUR_IN_SECONDS, function (): ?string {
            $settings = $this->settings->get();
            return $settings->enabled ? $settings->greeting : null;
        });
    }

    public function refresh(): void
    {
        $this->cache->forget('greeting');
        $this->current();
    }
}
