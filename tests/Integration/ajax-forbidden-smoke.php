<?php

$viewer = get_user_by('login', 'starterkit_viewer');
if (!$viewer instanceof WP_User) {
    $userId = wp_create_user('starterkit_viewer', wp_generate_password(), 'viewer@example.com');
    $viewer = get_user_by('id', $userId);
}
wp_set_current_user($viewer->ID);
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    '_ajax_nonce' => wp_create_nonce('starterkit_mvc_update_settings'),
    'greeting' => 'should not save',
    'enabled' => '1',
];
do_action('wp_ajax_starterkit_mvc_update_settings');
