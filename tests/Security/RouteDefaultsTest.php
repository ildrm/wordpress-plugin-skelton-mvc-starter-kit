<?php
declare(strict_types=1);

namespace StarterKit\Mvc\Tests\Security;

use PHPUnit\Framework\TestCase;
use StarterKit\Mvc\Routing\Route;

final class RouteDefaultsTest extends TestCase
{
    public function testRoutesDoNotStartPublic(): void
    {
        $route = new Route('GET', '/items', [self::class, 'testRoutesDoNotStartPublic']);
        self::assertFalse($route->public);
        self::assertNull($route->capability);
        self::assertNull($route->nonce);
    }
}
