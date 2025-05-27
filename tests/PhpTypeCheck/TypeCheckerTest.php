<?php

namespace Selfphp\PhpTypeCheck\Tests;

use PHPUnit\Framework\TestCase;
use Selfphp\PhpTypeCheck\TypeChecker;
use Selfphp\PhpTypeCheck\Exception\TypeCheckException;

class TypeCheckerTest extends TestCase
{
    public function testValidFlatIntArray(): void
    {
        TypeChecker::assertArrayOfType([1, 2, 3], 'int');
        $this->assertTrue(true);
    }

    public function testThrowsTypeCheckException(): void
    {
        $this->expectException(TypeCheckException::class);
        $this->expectExceptionMessage('expected int');

        TypeChecker::assertArrayOfType([1, 'fail', 3], 'int');
    }

    public function testDescribeFlatArray(): void
    {
        $this->assertSame('array<int>', TypeChecker::describeType([1, 2, 3]));
        $this->assertSame('array<string>', TypeChecker::describeType(['a', 'b']));
    }

    public function testDescribeMixedArray(): void
    {
        $this->assertSame('array<int|string>', TypeChecker::describeType([1, 'x']));
    }

    public function testManualCatch(): void
    {
        try {
            TypeChecker::assertArrayOfType([1, 'x'], 'int');
            $this->fail('Exception not thrown');
        } catch (TypeCheckException $e) {
            $this->assertStringContainsString('expected int', $e->getMessage());
            $this->assertSame('int', $e->expected);
            $this->assertSame('string', $e->actual);
            $this->assertSame('1', $e->path);
        }
    }
}
