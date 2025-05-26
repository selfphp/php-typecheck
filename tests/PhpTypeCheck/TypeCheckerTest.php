<?php

namespace Selfphp\TypeCheck\Tests;

use PHPUnit\Framework\TestCase;
use Selfphp\PhpTypeCheck\TypeChecker;

class TypeCheckerTest extends TestCase
{
    public function testValidIntegerArray(): void
    {
        TypeChecker::assertArrayOfType([1, 2, 3], 'int');
        $this->assertTrue(true);
    }

    public function testMixedArrayThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        TypeChecker::assertArrayOfType([1, 'string', 3], 'int');
    }

    public function testRecursiveArray(): void
    {
        TypeChecker::assertArrayOfType([[1], [2, 3]], 'int', true);
        $this->assertTrue(true);
    }

    public function testDescribeType(): void
    {
        $this->assertSame('int', TypeChecker::describeType(5));
        $this->assertSame('array<string>', TypeChecker::describeType(['a', 'b']));
    }
}
