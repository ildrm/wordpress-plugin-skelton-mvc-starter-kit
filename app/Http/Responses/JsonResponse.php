<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Http\Responses;

final readonly class JsonResponse
{
    public function __construct(public array $data, public int $status = 200)
    {
    }

    public function toRest(): \WP_REST_Response
    {
        return new \WP_REST_Response($this->data, $this->status);
    }
}
