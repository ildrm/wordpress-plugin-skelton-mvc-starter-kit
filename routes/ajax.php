<?php

declare(strict_types=1);

use StarterKit\Mvc\Http\Controllers\Ajax\SettingsController;
use StarterKit\Mvc\Http\Requests\UpdateSettingsRequest;
use StarterKit\Mvc\Routing\AjaxRouter;

return static function (AjaxRouter $router): void {
    $router->post('starterkit_mvc_update_settings', [SettingsController::class, 'update'])
        ->capability('manage_options')->nonce('starterkit_mvc_update_settings')->request(UpdateSettingsRequest::class);
};
