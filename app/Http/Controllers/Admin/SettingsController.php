<?php

declare(strict_types=1);

namespace StarterKit\Mvc\Http\Controllers\Admin;

use StarterKit\Mvc\Actions\UpdateSettings;
use StarterKit\Mvc\Contracts\Repositories\SettingsRepository;
use StarterKit\Mvc\Contracts\View\ViewFactory;
use StarterKit\Mvc\Data\SettingsData;
use StarterKit\Mvc\Http\Controllers\Controller;
use StarterKit\Mvc\Http\Requests\Request;
use StarterKit\Mvc\Http\Requests\UpdateSettingsRequest;
use StarterKit\Mvc\Http\Responses\RedirectResponse;
use StarterKit\Mvc\Http\Responses\ViewResponse;
use StarterKit\Mvc\Http\Validation\Validator;

final class SettingsController extends Controller
{
    public function __construct(private SettingsRepository $settings, private ViewFactory $views, private UpdateSettings $update, private Validator $validator)
    {
    }

    public function index(Request $request): ViewResponse
    {
        return new ViewResponse($this->views->make('admin.settings', ['settings' => $this->settings->get()]));
    }

    public function save(UpdateSettingsRequest $request): RedirectResponse
    {
        ($this->update)(SettingsData::fromArray($request->validated($this->validator)));
        return new RedirectResponse(add_query_arg('updated', '1', admin_url('admin.php?page=wordpress-mvc-starter')));
    }
}
