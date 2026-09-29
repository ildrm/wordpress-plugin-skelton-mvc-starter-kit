<?php
declare(strict_types=1);

namespace StarterKit\Mvc\Tests\Unit;

use PHPUnit\Framework\TestCase;
use StarterKit\Mvc\Exceptions\ViewNotFoundException;
use StarterKit\Mvc\Foundation\Application;
use StarterKit\Mvc\View\ViewFinder;

final class ViewTest extends TestCase
{
    public function testDotNotationAndPathTraversalRejection(): void
    {
        $app = new Application(dirname(__DIR__, 2), __FILE__, '1.0.0');
        $finder = new ViewFinder($app);
        self::assertSame($app->resourcePath('views/admin/settings.php'), $finder->find('admin.settings'));
        $this->expectException(ViewNotFoundException::class);
        $finder->find('../config.plugin');
    }
}
