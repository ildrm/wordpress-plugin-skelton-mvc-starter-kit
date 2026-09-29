<?php

declare(strict_types=1);

namespace StarterKit\Mvc\View;

final readonly class View
{
    public function __construct(private string $file, private ViewData $data)
    {
    }

    public function render(): string
    {
        $data = $this->data;
        ob_start();
        try {
            require $this->file;
            return (string) ob_get_clean();
        } catch (\Throwable $exception) {
            ob_end_clean();
            throw $exception;
        }
    }
}
