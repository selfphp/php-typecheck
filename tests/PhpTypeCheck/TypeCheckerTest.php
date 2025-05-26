<?php

namespace Selfphp\PhpTypeCheck\Tests;

use PHPUnit\Framework\TestCase;
use Selfphp\PhpTypeCheck\TypeChecker;

class TypeCheckerTest extends TestCase
{
    public function testValidFlatIntArray(): void
    {
        TypeChecker::assertArrayOfType([1, 2, 3], 'int');
        $this->assertTrue(true);
    }

    public function testValidNestedIntArray(): void
    {
        TypeChecker::assertArrayOfType([[1], [2, 3]], 'int', true);
        $this->assertTrue(true);
    }

    public function testFailsWithMixedTypes(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('expected int');
        TypeChecker::assertArrayOfType([1, 'fail', 3], 'int');
    }

    public function testFailsWithWrongNestedTypes(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('expected int');
        TypeChecker::assertArrayOfType([[1], ['x']], 'int', true);
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
        } catch (\InvalidArgumentException $e) {
            $this->assertStringContainsString('expected int', $e->getMessage());
        }
    }
}
