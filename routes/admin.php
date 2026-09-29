<?php

declare(strict_types=1);

use StarterKit\Mvc\Http\Controllers\Admin\SettingsController;
use StarterKit\Mvc\Http\Requests\UpdateSettingsRequest;
use StarterKit\Mvc\Routing\AdminRouter;

return static function (AdminRouter $router): void {
    $router->menu(__('MVC Starter', 'wordpress-mvc-starter'), __('MVC Starter', 'wordpress-mvc-starter'), 'manage_options', 'wordpress-mvc-starter', [SettingsController::class, 'index']);
    $router->post('starterkit_mvc_save_settings', [SettingsController::class, 'save'])
        ->capability('manage_options')->nonce('starterkit_mvc_save_settings')->request(UpdateSettingsRequest::class);
};
