<?php
declare(strict_types=1);

namespace StarterKit\Mvc\Tests\Unit;

use PHPUnit\Framework\TestCase;
use StarterKit\Mvc\Foundation\Configuration\Repository;

final class ConfigurationTest extends TestCase
{
    public function testDotNotationAndDefaults(): void
    {
        $config = new Repository(['plugin' => ['version' => '1.0']]);
        self::assertSame('1.0', $config->get('plugin.version'));
        self::assertSame('fallback', $config->get('plugin.missing', 'fallback'));
        $config->set('plugin.version', '2.0');
        self::assertSame('2.0', $config->get('plugin.version'));
    }
}
