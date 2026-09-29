<?php

declare(strict_types=1);

use StarterKit\Mvc\Foundation\Application;

$app = new Application(dirname(__DIR__), STARTERKIT_MVC_FILE, STARTERKIT_MVC_VERSION);
$app->loadConfiguration();
foreach (require __DIR__ . '/providers.php' as $provider) {
    $app->register($provider);
}

return $app;
