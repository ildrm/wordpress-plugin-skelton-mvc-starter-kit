<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Http\Requests;

final class UpdateSettingsRequest extends Request
{
    public function authorize(): bool
    {
        return current_user_can('manage_options');
    }
    public function rules(): array
    {
        return ['greeting' => ['required', 'string', 'max:120'], 'enabled' => ['required', 'boolean']];
    }
}
