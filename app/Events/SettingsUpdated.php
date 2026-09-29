<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Events;

use StarterKit\Mvc\Data\SettingsData;

final readonly class SettingsUpdated
{
    public function __construct(public SettingsData $settings)
    {
    }
}
