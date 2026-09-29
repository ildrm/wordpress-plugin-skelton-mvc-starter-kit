<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Actions;

use StarterKit\Mvc\Contracts\Events\Dispatcher;
use StarterKit\Mvc\Contracts\Cache\CacheStore;
use StarterKit\Mvc\Contracts\Repositories\SettingsRepository;
use StarterKit\Mvc\Data\SettingsData;
use StarterKit\Mvc\Events\SettingsUpdated;

final class UpdateSettings
{
    public function __construct(private SettingsRepository $settings, private Dispatcher $events, private CacheStore $cache)
    {
    }

    public function __invoke(SettingsData $data): SettingsData
    {
        $this->settings->save($data);
        $this->cache->forget('greeting');
        $this->events->dispatch(new SettingsUpdated($data));
        do_action('starterkit_mvc/settings/updated', $data->toArray());
        return $data;
    }
}
