<?php
declare(strict_types=1);

namespace StarterKit\Mvc\Tests\Security;

use PHPUnit\Framework\TestCase;
use StarterKit\Mvc\Database\Connection;

final class TableIdentifierTest extends TestCase
{
    public function testTableNamesUseSitePrefixAndRejectInjectedIdentifiers(): void
    {
        $connection = new Connection(new \wpdb());
        self::assertSame('wp_starterkit_items', $connection->table('starterkit_items'));
        $this->expectException(\InvalidArgumentException::class);
        $connection->table('items; DROP TABLE users');
    }
}
