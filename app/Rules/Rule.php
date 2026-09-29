<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Rules;

interface Rule
{
    public function passes(mixed $value): bool;
    public function message(): string;
}
