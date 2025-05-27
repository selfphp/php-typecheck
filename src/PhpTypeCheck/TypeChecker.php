<?php

/**
 * @package Selfphp\PhpTypeCheck
 * @author SELFPHP
 * @license MIT
 * @link https://github.com/selfphp/php-typecheck
 */

declare(strict_types=1);

namespace Selfphp\PhpTypeCheck;

use Selfphp\PhpTypeCheck\Exception\TypeCheckException;

/**
 * TypeChecker validates array elements and data structures against expected types.
 */
final class TypeChecker
{
    /**
     * Asserts that all elements in an array match the expected type.
     *
     * @param array $array The array to validate
     * @param string $expectedType The expected type (e.g., 'int', 'string', MyClass::class)
     * @param bool $recursive Whether to validate nested arrays recursively
     * @param string $path Internal path tracker for error messages
     * @throws TypeCheckException if any element does not match the expected type
     */
    public static function assertArrayOfType(array $array, string $expectedType, bool $recursive = false, string $path = ''): void
    {
        $expectedType = strtolower(trim($expectedType));

        foreach ($array as $key => $value) {
            $currentPath = $path === '' ? (string)$key : $path . "[$key]";

            if ($recursive && is_array($value)) {
                self::assertArrayOfType($value, $expectedType, true, $currentPath);
                continue;
            }

            if (class_exists($expectedType) || interface_exists($expectedType)) {
                $isValid = $value instanceof $expectedType;
            } else {
                $isValid = match ($expectedType) {
                    'int' => is_int($value),
                    'string' => is_string($value),
                    'float' => is_float($value),
                    'bool' => is_bool($value),
                    'array' => is_array($value),
                    'object' => is_object($value),
                    'callable' => is_callable($value),
                    'mixed' => true,
                    default => throw new TypeCheckException("Unknown type: $expectedType")
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

    /**
     * Checks whether all elements in an array match the expected type.
     *
     * @param array $array
     * @param string $expectedType
     * @param bool $recursive
     * @param string $path
     * @return bool
     */
    public static function checkArrayOfType(array $array, string $expectedType, bool $recursive = false, string $path = ''): bool
    {
        $expectedType = strtolower(trim($expectedType));

        foreach ($array as $key => $value) {
            $currentPath = $path === '' ? (string)$key : $path . "[$key]";

            if ($recursive && is_array($value)) {
                if (!self::checkArrayOfType($value, $expectedType, true, $currentPath)) {
                    return false;
                }
                continue;
            }

            if (class_exists($expectedType) || interface_exists($expectedType)) {
                $isValid = $value instanceof $expectedType;
            } else {
                $isValid = match ($expectedType) {
                    'int' => is_int($value),
                    'string' => is_string($value),
                    'float' => is_float($value),
                    'bool' => is_bool($value),
                    'array' => is_array($value),
                    'object' => is_object($value),
                    'callable' => is_callable($value),
                    'mixed' => true,
                    default => false
                };
            }

            if (!$isValid) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validates a structured array (associative) against a schema.
     *
     * @param array $data
     * @param array $schema
     * @param string $path
     * @throws TypeCheckException
     */
    public static function assertStructure(array $data, array $schema, string $path = ''): void
    {
        foreach ($schema as $key => $expectedType) {
            self::assertStructureKey($data, $key, $expectedType, $path);
        }
    }

    /**
     * Validates a single key from schema within the data structure.
     *
     * @param array $data
     * @param string $key
     * @param mixed $expectedType
     * @param string $path
     * @throws TypeCheckException
     */
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
        } else {
            self::assertArrayOfType([$value], $expectedType, false, $currentPath);
        }
    }

    /**
     * Soft-validates a structured array against a schema.
     *
     * @param array $data
     * @param array $schema
     * @param string $path
     * @return bool
     */
    public static function checkStructure(array $data, array $schema, string $path = ''): bool
    {
        foreach ($schema as $key => $expectedType) {
            if (!self::checkStructureKey($data, $key, $expectedType, $path)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Checks one key of a structure for existence and type validity.
     *
     * @param array $data
     * @param string $key
     * @param mixed $expectedType
     * @param string $path
     * @return bool
     */
    private static function checkStructureKey(array $data, string $key, mixed $expectedType, string $path): bool
    {
        $isOptional = str_ends_with($key, '?');
        $keyName = $isOptional ? rtrim($key, '?') : $key;
        $currentPath = $path === '' ? $keyName : $path . "[$keyName]";

        if (!array_key_exists($keyName, $data)) {
            return $isOptional;
        }

        $value = $data[$keyName];

        if (is_array($expectedType)) {
            return is_array($value) && self::checkStructure($value, $expectedType, $currentPath);
        }

        return self::checkArrayOfType([$value], $expectedType, false, $currentPath);
    }

    /**
     * Describes the type of a given value as string.
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
