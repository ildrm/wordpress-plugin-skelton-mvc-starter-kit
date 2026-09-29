<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Http\Controllers\Ajax;

use StarterKit\Mvc\Actions\UpdateSettings;
use StarterKit\Mvc\Data\SettingsData;
use StarterKit\Mvc\Http\Controllers\Controller;
use StarterKit\Mvc\Http\Requests\UpdateSettingsRequest;
use StarterKit\Mvc\Http\Responses\JsonResponse;
use StarterKit\Mvc\Http\Validation\Validator;

final class SettingsController extends Controller
{
    public function __construct(private UpdateSettings $update, private Validator $validator)
    {
    }

    public function update(UpdateSettingsRequest $request): JsonResponse
    {
        $settings = ($this->update)(SettingsData::fromArray($request->validated($this->validator)));
        return new JsonResponse($settings->toArray());
    }
}
