<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Contracts\View;

use StarterKit\Mvc\View\View;

interface ViewFactory
{
    public function make(string $name, array $data = []): View;
}
