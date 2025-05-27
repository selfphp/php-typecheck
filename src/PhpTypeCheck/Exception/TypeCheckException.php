<?php

declare(strict_types=1);

namespace Selfphp\PhpTypeCheck\Exception;

use InvalidArgumentException;

/**
 * Exception thrown when a type check fails.
 */
class TypeCheckException extends InvalidArgumentException
{
    public function __construct(
        string $message,
        public readonly string $path = '',
        public readonly string $expected = '',
        public readonly string $actual = '',
    ) {
        parent::__construct($message);
    }
}
