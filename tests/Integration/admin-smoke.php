<?php
declare(strict_types=1);

if (!has_action('admin_post_starterkit_mvc_save_settings')) {
    throw new RuntimeException('Admin form handler was not registered.');
}
if (!has_action('wp_ajax_starterkit_mvc_update_settings')) {
    throw new RuntimeException('Protected AJAX handler was not registered.');
}
if (has_action('wp_ajax_nopriv_starterkit_mvc_update_settings')) {
    throw new RuntimeException('Protected AJAX handler was exposed to anonymous users.');
}
do_action('admin_menu');
global $menu;
$slugs = array_column($menu, 2);
if (!in_array('wordpress-mvc-starter', $slugs, true)) {
    throw new RuntimeException('Admin menu was not registered.');
}
WP_CLI::success('Admin menu, form handler, and protected AJAX registration passed.');
