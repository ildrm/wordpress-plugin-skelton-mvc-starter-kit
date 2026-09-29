<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Data;

final readonly class SettingsData
{
    public function __construct(public string $greeting, public bool $enabled)
    {
    }

    public static function fromArray(array $value): self
    {
        return new self(sanitize_text_field($value['greeting']), filter_var($value['enabled'], FILTER_VALIDATE_BOOLEAN));
    }

    public function toArray(): array
    {
        return ['greeting' => $this->greeting, 'enabled' => $this->enabled];
    }
}
