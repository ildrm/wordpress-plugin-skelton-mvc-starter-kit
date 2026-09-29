<?php
declare(strict_types=1);

namespace StarterKit\Mvc\Tests\Integration;

use StarterKit\Mvc\WordPress\Lifecycle\Activator;
use StarterKit\Mvc\WordPress\Lifecycle\Deactivator;

final class SettingsFlowTest extends \WP_UnitTestCase
{
    public function testAuthorizedRestUpdateSanitizesAndPersistsSettings(): void
    {
        wp_set_current_user(self::factory()->user->create(['role' => 'administrator']));
        $request = new \WP_REST_Request('POST', '/wordpress-mvc-starter/v1/settings');
        $request->set_param('greeting', '<b>Hello</b>');
        $request->set_param('enabled', true);
        $response = rest_do_request($request);
        self::assertSame(200, $response->get_status());
        self::assertSame('Hello', get_option('starterkit_mvc_settings')['greeting']);
    }

    public function testInvalidRestUpdateDoesNotChangeStoredSettings(): void
    {
        wp_set_current_user(self::factory()->user->create(['role' => 'administrator']));
        $before = get_option('starterkit_mvc_settings');
        $request = new \WP_REST_Request('POST', '/wordpress-mvc-starter/v1/settings');
        $request->set_param('greeting', str_repeat('x', 121));
        $request->set_param('enabled', true);
        $response = rest_do_request($request);
        self::assertSame(400, $response->get_status());
        self::assertSame($before, get_option('starterkit_mvc_settings'));
    }

    public function testActivationIsRepeatableAndDeactivationUnschedulesJobs(): void
    {
        $app = require dirname(__DIR__, 2) . '/bootstrap/app.php';
        $activator = $app->make(Activator::class);
        $activator->activate();
        $activator->activate();
        self::assertNotFalse(wp_next_scheduled('starterkit_mvc_run_job', [\StarterKit\Mvc\Jobs\RefreshGreetingCache::class]));
        $app->make(Deactivator::class)->deactivate();
        self::assertFalse(wp_next_scheduled('starterkit_mvc_run_job', [\StarterKit\Mvc\Jobs\RefreshGreetingCache::class]));
    }
}
