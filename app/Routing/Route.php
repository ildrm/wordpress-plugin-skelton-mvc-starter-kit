<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Routing;

final class Route
{
    public ?string $capability = null;
    public bool $public = false;
    public ?string $nonce = null;
    public ?string $requestClass = null;
    public ?array $policy = null;
    public array $args = [];

    public function __construct(public string $method, public string $path, public array $handler)
    {
    }

    public function capability(string $capability): self
    {
        $this->capability = $capability;
        return $this;
    }
    public function public(): self
    {
        $this->public = true;
        return $this;
    }
    public function nonce(string $action): self
    {
        $this->nonce = $action;
        return $this;
    }
    public function request(string $class): self
    {
        $this->requestClass = $class;
        return $this;
    }

    public function policy(string $class, string $ability): self
    {
        $this->policy = [$class, $ability];
        return $this;
    }
    public function args(array $args): self
    {
        $this->args = $args;
        return $this;
    }
}
