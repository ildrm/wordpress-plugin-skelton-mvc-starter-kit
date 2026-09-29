<?php

/**
 * Plugin Name: WordPress MVC Starter
 * Description: A WordPress-native MVC application starter kit.
 * Version: 1.0.0
 * Requires at least: 6.5
 * Requires PHP: 8.2
 * Author: Shahin Ilderemi
 * Author URI: https://ildrm.com
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wordpress-mvc-starter
 * Domain Path: /languages
 */

defined('ABSPATH') || exit;

define('STARTERKIT_MVC_FILE', __FILE__);
define('STARTERKIT_MVC_VERSION', '1.0.0');

if (PHP_VERSION_ID < 80200 || version_compare($GLOBALS['wp_version'] ?? '0', '6.5', '<')) {
    add_action('admin_notices', static function (): void {
        echo '<div class="notice notice-error"><p>' . esc_html__('WordPress MVC Starter requires PHP 8.2 and WordPress 6.5 or newer.', 'wordpress-mvc-starter') . '</p></div>';
    });
    return;
}

if (!is_file(__DIR__ . '/vendor/autoload.php')) {
    add_action('admin_notices', static function (): void {
        echo '<div class="notice notice-error"><p>'
            . esc_html__('WordPress MVC Starter is missing its Composer autoloader. Install the release ZIP or run composer install.', 'wordpress-mvc-starter')
            . '</p></div>';
    });
    return;
}

require __DIR__ . '/vendor/autoload.php';

$starterkit_app = require __DIR__ . '/bootstrap/app.php';
register_activation_hook(__FILE__, [$starterkit_app->make(\StarterKit\Mvc\WordPress\Lifecycle\Activator::class), 'activate']);
register_deactivation_hook(__FILE__, [$starterkit_app->make(\StarterKit\Mvc\WordPress\Lifecycle\Deactivator::class), 'deactivate']);
$starterkit_app->boot();
