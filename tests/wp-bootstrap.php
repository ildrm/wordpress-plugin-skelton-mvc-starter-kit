<?php
declare(strict_types=1);

$testsDirectory = getenv('WP_TESTS_DIR');
if (!$testsDirectory || !is_file($testsDirectory . '/includes/bootstrap.php')) {
    throw new RuntimeException('Set WP_TESTS_DIR to the WordPress test library directory.');
}
require dirname(__DIR__) . '/vendor/autoload.php';
require $testsDirectory . '/includes/functions.php';
tests_add_filter('muplugins_loaded', static function (): void {
    require dirname(__DIR__) . '/plugin-name.php';
});
require $testsDirectory . '/includes/bootstrap.php';
