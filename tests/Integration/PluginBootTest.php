<?php
declare(strict_types=1);

namespace StarterKit\Mvc\Tests\Integration;

final class PluginBootTest extends \WP_UnitTestCase
{
    public function testRestRouteIsRegisteredAndProtected(): void
    {
        wp_set_current_user(0);
        do_action('rest_api_init');
        $routes = rest_get_server()->get_routes();
        self::assertArrayHasKey('/wordpress-mvc-starter/v1/settings', $routes);
        $request = new \WP_REST_Request('GET', '/wordpress-mvc-starter/v1/settings');
        $response = rest_do_request($request);
        self::assertSame(401, $response->get_status());
    }
}
