<?php
declare(strict_types=1);

$app = require dirname(__DIR__, 2) . '/bootstrap/app.php';
$activator = $app->make(\StarterKit\Mvc\WordPress\Lifecycle\Activator::class);
$deactivator = $app->make(\StarterKit\Mvc\WordPress\Lifecycle\Deactivator::class);
$lock = $app->make(\StarterKit\Mvc\WordPress\Lifecycle\MigrationLock::class);
$firstToken = $lock->acquire();
if ($firstToken === null || $lock->acquire() !== null) {
    throw new RuntimeException('Migration lock allowed concurrent ownership.');
}
$lock->release($firstToken);
add_option(\StarterKit\Mvc\WordPress\Lifecycle\MigrationLock::OPTION, '1:expired', '', false);
$reclaimed = $lock->acquire();
if ($reclaimed === null) {
    throw new RuntimeException('Expired migration lock could not be reclaimed.');
}
$lock->release($reclaimed);
$before = get_option('starterkit_mvc_settings');
$activator->activate();
$activator->activate();
if ($before !== get_option('starterkit_mvc_settings')) {
    throw new RuntimeException('Repeat activation changed settings.');
}
$deactivator->deactivate();
if (wp_next_scheduled('starterkit_mvc_run_job', [\StarterKit\Mvc\Jobs\RefreshGreetingCache::class])) {
    throw new RuntimeException('Deactivation left a scheduled job.');
}
if ($before !== get_option('starterkit_mvc_settings')) {
    throw new RuntimeException('Deactivation removed saved settings.');
}
$app->make(\StarterKit\Mvc\WordPress\Lifecycle\Uninstaller::class)->uninstall();
if ($before !== get_option('starterkit_mvc_settings')) {
    throw new RuntimeException('Default uninstall removed saved settings.');
}
$activator->activate();
WP_CLI::success('Repeat activation, deactivation, uninstall preservation, and migration locking passed.');
