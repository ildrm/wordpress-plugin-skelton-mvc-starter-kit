<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Http\Requests;

use StarterKit\Mvc\Exceptions\AuthorizationException;
use StarterKit\Mvc\Http\Validation\Validator;

class Request
{
    final public function __construct(
        protected array $input = [],
        protected array $route = [],
        protected array $query = [],
        protected array $files = [],
        protected array $headers = [],
        protected string $method = 'GET',
        protected ?\WP_User $user = null
    ) {
        $this->headers = array_change_key_case($headers, CASE_LOWER);
    }

    public static function fromRest(\WP_REST_Request $request): static
    {
        return new static(
            $request->get_params(),
            $request->get_url_params(),
            $request->get_query_params(),
            $request->get_file_params(),
            $request->get_headers(),
            $request->get_method(),
            wp_get_current_user()
        );
    }

    public static function fromGlobals(): static
    {
        // The admin/AJAX routers verify the nonce before this factory is called.
        // phpcs:ignore WordPress.Security.NonceVerification.Missing
        $body = wp_unslash($_POST);
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading query data is not a state change.
        $query = wp_unslash($_GET);
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (str_starts_with($name, 'HTTP_') && is_string($value)) {
                $headers[strtolower(str_replace('_', '-', substr($name, 5)))] = $value;
            }
        }
        $method = isset($_SERVER['REQUEST_METHOD']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_METHOD'])) : 'GET';
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- The router verifies the nonce before accessing files.
        return new static($body, [], $query, $_FILES, $headers, $method, wp_get_current_user());
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->input[$key] ?? $default;
    }
    public function route(string $key, mixed $default = null): mixed
    {
        return $this->route[$key] ?? $default;
    }
    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }
    public function file(string $key, mixed $default = null): mixed
    {
        return $this->files[$key] ?? $default;
    }
    public function header(string $key, mixed $default = null): mixed
    {
        $value = $this->headers[strtolower($key)] ?? $default;
        return is_array($value) ? ($value[0] ?? $default) : $value;
    }
    public function method(): string
    {
        return $this->method;
    }
    public function user(): ?\WP_User
    {
        return $this->user;
    }
    public function nonce(): ?string
    {
        $value = $this->input('_wpnonce', $this->input('_ajax_nonce', $this->header('x-wp-nonce')));
        return is_string($value) ? $value : null;
    }
    public function all(): array
    {
        return $this->input;
    }
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [];
    }

    public function validated(Validator $validator): array
    {
        if (!$this->authorize()) {
            throw new AuthorizationException('Permission denied.');
        }
        return $validator->validate($this->input, $this->rules());
    }
}
