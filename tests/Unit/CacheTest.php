<?php
declare(strict_types=1);

namespace StarterKit\Mvc\Tests\Unit;

use PHPUnit\Framework\TestCase;
use StarterKit\Mvc\WordPress\Cache\WordPressCache;

final class CacheTest extends TestCase
{
    public function testFalseAndNullAreStoredAsValuesAndInvalidationWorks(): void
    {
        $GLOBALS['starterkit_test_transients'] = [];
        $cache = new WordPressCache();
        $cache->put('off', false, 60);
        $cache->put('empty', null, 60);
        self::assertFalse($cache->get('off', 'missing'));
        self::assertNull($cache->get('empty', 'missing'));
        $cache->forget('off');
        self::assertSame('missing', $cache->get('off', 'missing'));
    }
}
