<?php

namespace Selfphp\PhpTypeCheck\Tests;

use PHPUnit\Framework\TestCase;
use Selfphp\PhpTypeCheck\TypeChecker;
use Selfphp\PhpTypeCheck\Exception\TypeCheckException;

class TypeCheckerTest extends TestCase
{
    /**
     * Ensures that a flat array of integers passes validation.
     */
    public function testValidFlatIntArray(): void
    {
        TypeChecker::assertArrayOfType([1, 2, 3], 'int');
        $this->assertTrue(true);
    }

    /**
     * Verifies that a TypeCheckException is thrown for an invalid array element.
     */
    public function testThrowsTypeCheckException(): void
    {
        $this->expectException(TypeCheckException::class);
        $this->expectExceptionMessage('expected int');

        TypeChecker::assertArrayOfType([1, 'fail', 3], 'int');
    }

    /**
     * Verifies type description for a flat array of integers and strings.
     */
    public function testDescribeFlatArray(): void
    {
        $this->assertSame('array<int>', TypeChecker::describeType([1, 2, 3]));
        $this->assertSame('array<string>', TypeChecker::describeType(['a', 'b']));
    }

    /**
     * Verifies mixed type description for a heterogeneous array.
     */
    public function testDescribeMixedArray(): void
    {
        $this->assertSame('array<int|string>', TypeChecker::describeType([1, 'x']));
    }

    /**
     * Manually catches a TypeCheckException and checks details.
     */
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

    /**
     * Validates a flat associative structure with scalar values.
     */
    public function testValidStructureFlat(): void
    {
        $data = ['name' => 'Alice', 'age' => 30];
        $schema = ['name' => 'string', 'age' => 'int'];
        TypeChecker::assertStructure($data, $schema);
        $this->assertTrue(true);
    }

    /**
     * Validates a nested associative structure.
     */
    public function testValidStructureNested(): void
    {
        $data = ['user' => ['name' => 'Alice', 'active' => true]];
        $schema = ['user' => ['name' => 'string', 'active' => 'bool']];
        TypeChecker::assertStructure($data, $schema);
        $this->assertTrue(true);
    }

    /**
     * Ensures that optional keys can be omitted without error.
     */
    public function testStructureWithOptionalFieldMissing(): void
    {
        $data = ['email' => 'test@example.com'];
        $schema = ['email' => 'string', 'phone?' => 'string'];
        TypeChecker::assertStructure($data, $schema);
        $this->assertTrue(true);
    }

    /**
     * Ensures a missing required field triggers a TypeCheckException.
     */
    public function testStructureMissingRequiredField(): void
    {
        $this->expectException(TypeCheckException::class);
        $this->expectExceptionMessage('Missing required key');

        $data = ['name' => 'Alice'];
        $schema = ['name' => 'string', 'age' => 'int'];
        TypeChecker::assertStructure($data, $schema);
    }

    /**
     * Ensures an incorrect type in a required field triggers an exception.
     */
    public function testStructureWrongType(): void
    {
        $this->expectException(TypeCheckException::class);
        $this->expectExceptionMessage('expected int');

        $data = ['name' => 'Alice', 'age' => 'thirty'];
        $schema = ['name' => 'string', 'age' => 'int'];
        TypeChecker::assertStructure($data, $schema);
    }

    /**
     * Ensures an incorrect type in an optional field triggers an exception.
     */
    public function testStructureOptionalFieldWrongType(): void
    {
        $this->expectException(TypeCheckException::class);
        $this->expectExceptionMessage('expected string');

        $data = ['email' => 'test@example.com', 'phone' => 12345];
        $schema = ['email' => 'string', 'phone?' => 'string'];
        TypeChecker::assertStructure($data, $schema);
    }
}
