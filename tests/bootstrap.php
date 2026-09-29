<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

if (!class_exists('wpdb')) {
    class wpdb
    {
        public string $prefix = 'wp_';
    }
}

if (!function_exists('get_option')) {
    function get_option(string $name, mixed $default = false): mixed
    {
        return $GLOBALS['starterkit_test_options'][$name] ?? $default;
    }
    function update_option(string $name, mixed $value, bool $autoload = false): bool
    {
        $GLOBALS['starterkit_test_options'][$name] = $value;
        return true;
    }
}

if (!function_exists('__')) {
    function __(string $message, string $domain = ''): string
    {
        return $message;
    }
}

if (!function_exists('get_transient')) {
    function get_transient(string $key): mixed
    {
        return $GLOBALS['starterkit_test_transients'][$key] ?? false;
    }
    function set_transient(string $key, mixed $value, int $seconds): bool
    {
        $GLOBALS['starterkit_test_transients'][$key] = $value;
        return true;
    }
    function delete_transient(string $key): bool
    {
        unset($GLOBALS['starterkit_test_transients'][$key]);
        return true;
    }
}
