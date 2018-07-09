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
    public static function emptyPatterns(): self
    {
        return new self("At least one type pattern must be provided");
    }

    /**
     * The implicit type is missing from type patterns.
     *
     * @param string $implicit
     * @return InvalidTypes
     */
    public static function implicitTypeMissing(string $implicit): self
    {
        return new self("The implicit attribute type '$implicit' has no pattern");
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
        return new self("The pattern '$pattern' of attribute '$name' is invalid");
    }
}
