<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Http\Responses;

final readonly class RedirectResponse
{
    public function __construct(public string $url)
    {
    }
}
