<?php
declare(strict_types=1);

namespace StarterKit\Mvc\Tests\Architecture;

use PHPUnit\Framework\TestCase;

final class BoundariesTest extends TestCase
{
    public function testControllersAndViewsDoNotAccessTheDatabase(): void
    {
        foreach (['app/Http/Controllers', 'resources/views'] as $directory) {
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(dirname(__DIR__, 2) . '/' . $directory)) as $file) {
                if (!$file->isFile() || $file->getExtension() !== 'php') { continue; }
                $source = (string) file_get_contents($file->getPathname());
                self::assertDoesNotMatchRegularExpression('/\$wpdb|->query\(|get_option\(/', $source, $file->getPathname());
            }
        }
    }

    public function testEntryFileRemainsSmall(): void
    {
        $source = (string) file_get_contents(dirname(__DIR__, 2) . '/plugin-name.php');
        self::assertLessThan(90, count(explode("\n", $source)));
        self::assertDoesNotMatchRegularExpression('/\$wpdb|register_rest_route\(|add_menu_page\(/', $source);
    }
}
