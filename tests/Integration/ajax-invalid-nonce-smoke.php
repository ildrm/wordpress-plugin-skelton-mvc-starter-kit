<?php

$admin = get_user_by('login', 'starterkit');
wp_set_current_user($admin->ID);
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    '_ajax_nonce' => 'invalid',
    'greeting' => 'should not save',
    'enabled' => '1',
];
do_action('wp_ajax_starterkit_mvc_update_settings');
