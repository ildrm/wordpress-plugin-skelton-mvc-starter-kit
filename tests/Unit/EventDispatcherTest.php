<?php
declare(strict_types=1);

namespace StarterKit\Mvc\Tests\Unit;

use PHPUnit\Framework\TestCase;
use StarterKit\Mvc\Foundation\Application;
use StarterKit\Mvc\Support\EventDispatcher;

final readonly class SampleEvent { public function __construct(public int $value) {} }
final class SampleListener { public static array $seen = []; public function __invoke(SampleEvent $event): void { self::$seen[] = $event->value; } }

final class EventDispatcherTest extends TestCase
{
    public function testMultipleDependencyInjectedListenersReceiveTheEvent(): void
    {
        SampleListener::$seen = [];
        $events = new EventDispatcher(new Application(dirname(__DIR__, 2), __FILE__, '1.0.0'));
        $events->listen(SampleEvent::class, SampleListener::class);
        $events->listen(SampleEvent::class, SampleListener::class);
        $events->dispatch(new SampleEvent(7));
        self::assertSame([7, 7], SampleListener::$seen);
    }
}
