<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter;

use Svoboda\PsrRouter\Parser\Input;

/**
 * Invalid route specification.
 */
class InvalidRoute extends Exception
{
    /**
     * Invalid route with more attributes of the same name.
     *
     * @param string $path
     * @param string[] $names
     * @return InvalidRoute
     */
    public static function ambiguousAttribute(string $path, array $names): self
    {
        $names = array_map(function ($name) {
            return "'$name'";
        }, $names);

        $names = implode(", ", $names);

        return new self("Multiple attributes with name $names:\n$path");
    }

    /**
     * Invalid route with unexpected end.
     *
     * @param Input $path
     * @return InvalidRoute
     */
    public static function unexpectedEnd(Input $path): self
    {
        $pointer = self::createPointerString($path->getInput(), $path->getIndex());

        return new self("Unexpected end of route:\n$pointer");
    }

    /**
     * Invalid route with unexpected character.
     *
     * @param Input $path
     * @param string[] $expected
     * @return InvalidRoute
     */
    public static function unexpectedCharacter(Input $path, array $expected = []): self
    {
        $pointer = self::createPointerString($path->getInput(), $path->getIndex());

        if (empty($expected)) {
            return new self("Unexpected character:\n$pointer");
        }

        $expected = $expected ?? $path->getLatestExpectations();

        $expected = array_map(function (string $e) {
            return "'$e'";
        }, $expected);

        $expected = implode(", ", $expected);

        return new self("Unexpected character (expected $expected):\n$pointer");
    }

    /**
     * Invalid route where optional part is not at the very end.
     *
     * @param Input $path
     * @return InvalidRoute
     */
    public static function optionalIsNotSuffix(Input $path): self
    {
        $pointer = self::createPointerString($path->getInput(), $path->getIndex());

        return new self("Optional sequence cannot be followed by anything else:\n$pointer");
    }

    /**
     * Invalid route with empty attribute name.
     *
     * @param Input $path
     * @return InvalidRoute
     */
    public static function emptyAttributeName(Input $path): self
    {
        return self::emptyAttributeParameter($path, "name");
    }

    /**
     * Invalid route with too long attribute name.
     *
     * @param Input $path
     * @param int $maxLength
     * @return InvalidRoute
     */
    public static function tooLongAttributeName(Input $path, int $maxLength): self
    {
        return self::tooLongAttributeParameter($path, $maxLength, "name");
    }

    /**
     * Invalid route with empty attribute type.
     *
     * @param Input $path
     * @return InvalidRoute
     */
    public static function emptyAttributeType(Input $path): self
    {
        return self::emptyAttributeParameter($path, "type");
    }

    /**
     * Invalid route with too long attribute type.
     *
     * @param Input $path
     * @param int $maxLength
     * @return InvalidRoute
     */
    public static function tooLongAttributeType(Input $path, int $maxLength): self
    {
        return self::tooLongAttributeParameter($path, $maxLength, "type");
    }

    /**
     * Invalid route with invalid attribute parameter.
     *
     * @param Input $path
     * @param string $parameter
     * @return InvalidRoute
     */
    private static function emptyAttributeParameter(Input $path, string $parameter): self
    {
        $pointer = self::createPointerString($path->getInput(), $path->getIndex());

        return new self("The attribute $parameter is missing:\n$pointer");
    }

    /**
     * Invalid route with too long attribute parameter.
     *
     * @param Input $path
     * @param int $maxLength
     * @param string $parameter
     * @return InvalidRoute
     */
    private static function tooLongAttributeParameter(Input $path, int $maxLength, string $parameter): self
    {
        $pointer = self::createPointerString($path->getInput(), $path->getIndex() - 1);

        return new self("The attribute $parameter exceeded maximum allowed length of $maxLength characters:\n$pointer");
    }

    /**
     * Creates a string with an arrow pointing to the character after padding.
     *
     * @param string $path
     * @param int $padding
     * @return string
     */
    private static function createPointerString(string $path, int $padding): string
    {
        $pointer = str_repeat(" ", $padding) . "^";

        return $path . "\n" . $pointer;
    }
}
