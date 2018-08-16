<?php

declare(strict_types=1);

namespace Svoboda\Router\Types;

use Svoboda\Router\Exception;

/**
 * Invalid attribute types provided.
 */
class InvalidTypes extends Exception
{
    /**
     * Empty type patterns.
     *
     * @return InvalidTypes
     */
    public static function emptyCollection(): self
    {
        return new self("At least one type must be provided");
    }

    /**
     * Type has invalid name.
     *
     * @param string $name
     * @return InvalidTypes
     */
    public static function invalidTypeName(string $name): self
    {
        return new self("The type name '$name' is invalid, only alphanumeric characters and underscore are allowed");
    }

    /**
     * Type has invalid pattern.
     *
     * @param string $name
     * @param string $pattern
     * @return InvalidTypes
     */
    public static function invalidTypePattern(string $name, string $pattern): self
    {
        return new self("The pattern '$pattern' of type '$name' is invalid");
    }
}
