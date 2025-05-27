<?php

declare(strict_types=1);

namespace Selfphp\PhpTypeCheck;

use Selfphp\PhpTypeCheck\Exception\TypeCheckException;

/**
 * TypeChecker validates array elements against a given type, optionally recursively.
 */
final class TypeChecker
{
    /**
     * Asserts that all elements in an array match the expected type.
     *
     * @param array $array           The array to validate
     * @param string $expectedType   Expected type (e.g. 'int', 'string', User::class)
     * @param bool $recursive        Whether to check nested arrays recursively
     * @param string $path           Internal path for error reporting
     *
     * @throws TypeCheckException if any element does not match the type
     */
    public static function assertArrayOfType(
        array $array,
        string $expectedType,
        bool $recursive = false,
        string $path = ''
    ): void {

        $expectedType = strtolower(trim($expectedType));

        foreach ($array as $key => $value) {
            $currentPath = $path === '' ? (string)$key : $path . "[$key]";

            if ($recursive && is_array($value)) {
                self::assertArrayOfType($value, $expectedType, true, $currentPath);
                continue;
            }

            $isValid = false;

            if (class_exists($expectedType) || interface_exists($expectedType)) {
                $isValid = $value instanceof $expectedType;
            } else {
                $isValid = match ($expectedType) {
                    'int'     => is_int($value),
                    'string'  => is_string($value),
                    'float'   => is_float($value),
                    'bool'    => is_bool($value),
                    'array'   => is_array($value),
                    'object'  => is_object($value),
                    'callable'=> is_callable($value),
                    'mixed'   => true,
                    default   => throw new TypeCheckException("Unknown type: $expectedType")
                };
            }

            $actualType = get_debug_type($value);

            if (!$isValid) {
                throw new TypeCheckException(
                    "Element at [$currentPath] is of type $actualType, expected $expectedType",
                    $currentPath,
                    $expectedType,
                    $actualType
                );
            }
        }
    }

    public static function assertStructure(array $data, array $schema, string $path = ''): void
    {
        foreach ($schema as $key => $expectedType) {
            self::assertStructureKey($data, $key, $expectedType, $path);
        }
    }

    private static function assertStructureKey(array $data, string $key, mixed $expectedType, string $path): void
    {
        $isOptional = str_ends_with($key, '?');
        $keyName = $isOptional ? rtrim($key, '?') : $key;
        $currentPath = $path === '' ? $keyName : $path . "[$keyName]";

        if (!array_key_exists($keyName, $data)) {
            if ($isOptional) {
                return;
            }
            throw new TypeCheckException("Missing required key: $currentPath", $currentPath, (string) $expectedType, 'missing');
        }

        $value = $data[$keyName];

        if (is_array($expectedType)) {
            if (!is_array($value)) {
                throw new TypeCheckException("Expected array at $currentPath", $currentPath, 'array', get_debug_type($value));
            }
            self::assertStructure($value, $expectedType, $currentPath);
            return;
        }

        self::assertArrayOfType([$value], $expectedType, false, $currentPath);
    }

    /**
     * Returns a string description of the type of a value.
     * For example: 'array<int>', 'User', 'string', etc.
     *
     * @param mixed $value
     * @return string
     */
    public static function describeType(mixed $value): string
    {
        if (is_object($value)) {
            return get_class($value);
        }

        if (is_array($value)) {
            $types = array_map([self::class, 'describeType'], $value);
            $types = array_unique($types);
            return 'array<' . implode('|', $types) . '>';
        }

        return match (gettype($value)) {
            'integer' => 'int',
            'boolean' => 'bool',
            'double'  => 'float',
            default   => gettype($value),
        };
    }
}