<?php
declare(strict_types=1);

namespace StarterKit\Mvc\Tests\Unit;

use PHPUnit\Framework\TestCase;
use StarterKit\Mvc\Http\Requests\Request;

final class RequestTest extends TestCase
{
    public function testBoundaryDataHasSeparateAccessors(): void
    {
        $request = new Request(
            ['name' => 'Ada', '_wpnonce' => 'token'],
            ['id' => '7'],
            ['page' => '2'],
            ['upload' => ['name' => 'a.txt']],
            ['X-Trace' => ['abc']],
            'POST'
        );
        self::assertSame('Ada', $request->input('name'));
        self::assertSame('7', $request->route('id'));
        self::assertSame('2', $request->query('page'));
        self::assertSame('a.txt', $request->file('upload')['name']);
        self::assertSame('abc', $request->header('x-trace'));
        self::assertSame('POST', $request->method());
        self::assertSame('token', $request->nonce());
    }
}
