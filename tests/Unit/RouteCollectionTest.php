<?php
declare(strict_types=1);

namespace StarterKit\Mvc\Tests\Unit;

use PHPUnit\Framework\TestCase;
use StarterKit\Mvc\Routing\Route;
use StarterKit\Mvc\Routing\RouteCollection;

final class RouteCollectionTest extends TestCase
{
    public function testDuplicateMethodAndPathAreRejected(): void
    {
        $routes = new RouteCollection();
        $routes->add(new Route('GET', '/items', [self::class, 'testDuplicateMethodAndPathAreRejected']));
        $this->expectException(\LogicException::class);
        $routes->add(new Route('GET', '/items', [self::class, 'testDuplicateMethodAndPathAreRejected']));
    }
}
