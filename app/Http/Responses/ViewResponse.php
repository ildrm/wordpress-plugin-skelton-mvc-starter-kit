<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Http\Responses;

use StarterKit\Mvc\View\View;

final readonly class ViewResponse
{
    public function __construct(private View $view)
    {
    }

    public function render(): string
    {
        return $this->view->render();
    }
}
