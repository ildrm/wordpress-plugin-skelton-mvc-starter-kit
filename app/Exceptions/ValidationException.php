<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Exceptions;

final class ValidationException extends \RuntimeException
{
    public function __construct(private array $errors)
    {
        parent::__construct('The supplied data is invalid.');
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
