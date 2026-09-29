<?php

declare(strict_types=1);

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

if (!is_file(__DIR__ . '/vendor/autoload.php')) {
    return;
}

define('STARTERKIT_MVC_FILE', __DIR__ . '/plugin-name.php');
define('STARTERKIT_MVC_VERSION', '1.0.0');
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\StarterKit\Mvc\WordPress\Lifecycle\Uninstaller::class)->uninstall();
