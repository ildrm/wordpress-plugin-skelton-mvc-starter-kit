<?php

declare(strict_types=1);

use StarterKit\Mvc\Http\Controllers\Rest\SettingsController;
use StarterKit\Mvc\Http\Requests\UpdateSettingsRequest;
use StarterKit\Mvc\Policies\SettingsPolicy;
use StarterKit\Mvc\Routing\RestRouter;

return static function (RestRouter $router): void {
    $router->get('/settings', [SettingsController::class, 'show'])->capability('manage_options');
    $router->post('/settings', [SettingsController::class, 'update'])
        ->capability('manage_options')->policy(SettingsPolicy::class, 'update')->request(UpdateSettingsRequest::class)->args([
            'greeting' => ['type' => 'string', 'required' => true, 'maxLength' => 120],
            'enabled' => ['type' => 'boolean', 'required' => true],
        ]);
};
