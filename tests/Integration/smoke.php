<?php

if (get_option('starterkit_mvc_schema_version') !== '1.0.0') {
    throw new RuntimeException('Activation did not record the schema version.');
}
if (!wp_next_scheduled('starterkit_mvc_run_job', [\StarterKit\Mvc\Jobs\RefreshGreetingCache::class])) {
    throw new RuntimeException('Activation did not schedule the cache job.');
}
if (!shortcode_exists('starterkit_greeting')) {
    throw new RuntimeException('Shortcode was not registered.');
}
if (!WP_Block_Type_Registry::get_instance()->is_registered('starterkit-mvc/greeting')) {
    throw new RuntimeException('Block was not registered.');
}
if (!isset(rest_get_server()->get_routes()['/wordpress-mvc-starter/v1/settings'])) {
    throw new RuntimeException('REST route was not registered.');
}

wp_set_current_user(0);
$anonymous = rest_do_request(new WP_REST_Request('GET', '/wordpress-mvc-starter/v1/settings'));
if ($anonymous->get_status() !== 401) {
    throw new RuntimeException('Anonymous REST request was not denied.');
}

$viewer = get_user_by('login', 'starterkit_rest_viewer');
$viewerId = $viewer instanceof WP_User
    ? $viewer->ID
    : wp_create_user('starterkit_rest_viewer', wp_generate_password(), 'rest-viewer@example.com');
wp_set_current_user($viewerId);
$forbidden = rest_do_request(new WP_REST_Request('GET', '/wordpress-mvc-starter/v1/settings'));
if ($forbidden->get_status() !== 403) {
    throw new RuntimeException('REST request without manage_options was not denied.');
}

$admin = get_user_by('login', 'starterkit');
if (!$admin instanceof WP_User) {
    throw new RuntimeException('Test administrator is missing.');
}
wp_set_current_user($admin->ID);
$request = new WP_REST_Request('POST', '/wordpress-mvc-starter/v1/settings');
$request->set_param('greeting', '<b>Hello</b>');
$request->set_param('enabled', true);
$response = rest_do_request($request);
if ($response->get_status() !== 200 || get_option('starterkit_mvc_settings')['greeting'] !== 'Hello') {
    throw new RuntimeException('Authorized REST update did not sanitize and persist settings.');
}
$invalid = new WP_REST_Request('POST', '/wordpress-mvc-starter/v1/settings');
$invalid->set_param('greeting', str_repeat('x', 121));
$invalid->set_param('enabled', true);
$invalidResponse = rest_do_request($invalid);
if ($invalidResponse->get_status() !== 400 || get_option('starterkit_mvc_settings')['greeting'] !== 'Hello') {
    throw new RuntimeException('Invalid REST data changed saved settings.');
}
if (!str_contains(do_shortcode('[starterkit_greeting]'), 'Hello')) {
    throw new RuntimeException('Shortcode did not render updated greeting.');
}

WP_CLI::success('Plugin activation, Cron, block, shortcode, REST permissions, persistence, and rendering passed.');
