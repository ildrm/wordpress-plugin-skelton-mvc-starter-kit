<?php
declare(strict_types=1);

namespace StarterKit\Mvc\Tests\Unit;

use PHPUnit\Framework\TestCase;
use StarterKit\Mvc\Contracts\Database\Connection;
use StarterKit\Mvc\Database\Schema\Migrator;
use StarterKit\Mvc\Database\Schema\Schema;
use StarterKit\Mvc\Foundation\Application;

final class FakeConnection implements Connection
{
    public function table(string $name): string { return 'wp_' . $name; }
    public function select(string $sql, array $values = []): array { return []; }
    public function insert(string $table, array $data): int { return 0; }
    public function update(string $table, array $data, array $where): int { return 0; }
    public function delete(string $table, array $where): int { return 0; }
}

final class MigratorTest extends TestCase
{
    public function testMigrationsRunInOrderAndOnlyOnce(): void
    {
        $root = sys_get_temp_dir() . '/starterkit-migrations-' . bin2hex(random_bytes(5));
        mkdir($root . '/database/migrations', 0700, true);
        $GLOBALS['starterkit_test_options'] = [];
        $GLOBALS['starterkit_migration_log'] = [];
        foreach (['002_second', '001_first'] as $name) {
            file_put_contents($root . '/database/migrations/' . $name . '.php', '<?php return new class implements \\StarterKit\\Mvc\\Database\\Schema\\Migration { public function up(\\StarterKit\\Mvc\\Database\\Schema\\Schema $schema): void { $GLOBALS["starterkit_migration_log"][] = "' . $name . '"; } public function down(\\StarterKit\\Mvc\\Database\\Schema\\Schema $schema): void {} };');
        }
        try {
            $migrator = new Migrator(new Application($root, __FILE__, '1.0.0'), new Schema(new FakeConnection(), new \wpdb()));
            self::assertSame(['001_first', '002_second'], $migrator->run());
            self::assertSame([], $migrator->run());
            self::assertSame(['001_first', '002_second'], $GLOBALS['starterkit_migration_log']);
        } finally {
            foreach (glob($root . '/database/migrations/*.php') ?: [] as $file) { unlink($file); }
            rmdir($root . '/database/migrations');
            rmdir($root . '/database');
            rmdir($root);
        }
    }
}
