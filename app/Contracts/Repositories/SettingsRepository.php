<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Contracts\Repositories;

use StarterKit\Mvc\Data\SettingsData;

interface SettingsRepository
{
    public function get(): SettingsData;
    public function save(SettingsData $data): void;
    public function delete(): void;
}
