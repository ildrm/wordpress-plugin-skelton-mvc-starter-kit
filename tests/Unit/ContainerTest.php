<?php
declare(strict_types=1);

namespace StarterKit\Mvc\Tests\Unit;

use PHPUnit\Framework\TestCase;
use StarterKit\Mvc\Exceptions\BindingResolutionException;
use StarterKit\Mvc\Foundation\Container;

interface Clock { public function now(): int; }
final class FixedClock implements Clock { public function now(): int { return 42; } }
final class NeedsClock { public function __construct(public Clock $clock) {} }
final class CircularA { public function __construct(public CircularB $b) {} }
final class CircularB { public function __construct(public CircularA $a) {} }
final class Greeter { public function hello(Clock $clock, string $name): string { return $name . $clock->now(); } }

final class ContainerTest extends TestCase
{
    public function testAutoResolutionAndInterfaceBinding(): void
    {
        $container = new Container();
        $container->bind(Clock::class, FixedClock::class);
        self::assertSame(42, $container->make(NeedsClock::class)->clock->now());
        self::assertNotSame($container->make(NeedsClock::class), $container->make(NeedsClock::class));
    }

    public function testSingletonAndMethodInjection(): void
    {
        $container = new Container();
        $container->singleton(Clock::class, FixedClock::class);
        self::assertSame($container->make(Clock::class), $container->make(Clock::class));
        self::assertSame('Ada42', $container->call([$container->make(Greeter::class), 'hello'], ['name' => 'Ada']));
    }

    public function testCircularDependencyIsReported(): void
    {
        $this->expectException(BindingResolutionException::class);
        $this->expectExceptionMessage('Circular dependency');
        (new Container())->make(CircularA::class);
    }
}
