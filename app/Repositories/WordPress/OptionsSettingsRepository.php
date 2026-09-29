<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Repositories\WordPress;

use StarterKit\Mvc\Contracts\Repositories\SettingsRepository;
use StarterKit\Mvc\Data\SettingsData;

final class OptionsSettingsRepository implements SettingsRepository
{
    public const OPTION = 'starterkit_mvc_settings';

    public function get(): SettingsData
    {
        $raw = get_option(self::OPTION, []);
        $raw = is_array($raw) ? $raw : [];
        return new SettingsData(
            is_string($raw['greeting'] ?? null) ? $raw['greeting'] : __('Hello from WordPress MVC Starter', 'wordpress-mvc-starter'),
            (bool) ($raw['enabled'] ?? true)
        );
    }

    public function save(SettingsData $data): void
    {
        update_option(self::OPTION, $data->toArray(), false);
    }

    public function delete(): void
    {
        delete_option(self::OPTION);
    }
}
