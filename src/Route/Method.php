<?php

declare(strict_types=1);

namespace Svoboda\Router\Route;

/**
 * Represents HTTP methods.
 */
class Method
{
    public const OPTIONS = "OPTIONS";

    public const GET = "GET";

    public const HEAD = "HEAD";

    public const POST = "POST";

    public const PUT = "PUT";

    public const PATCH = "PATCH";

    public const DELETE = "DELETE";

    /**
     * Returns all recognized methods.
     *
     * @return string[]
     */
    public static function all(): array
    {
        return [
            self::OPTIONS,
            self::GET,
            self::HEAD,
            self::POST,
            self::PUT,
            self::PATCH,
            self::DELETE,
        ];
    }

    /**
     * Determines whether the given method is valid or not.
     *
     * @param string $method
     * @return bool
     */
    public static function isValid(string $method): bool
    {
        return in_array($method, self::all());
    }

    /**
     * Determine if the given method is always allowed.
     *
     * @param string $method
     * @return bool
     */
    public static function isAlwaysAllowed(string $method): bool
    {
        return in_array($method, [self::HEAD, self::GET]);
    }
}
