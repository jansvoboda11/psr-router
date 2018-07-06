<?php

declare(strict_types=1);

namespace Svoboda\Router\Route;

use Svoboda\Router\Exception;
use Svoboda\Router\Parser\Input;

/**
 * Invalid route definition.
 */
class InvalidRoute extends Exception
{
    /**
     * Invalid route with more attributes of the same name.
     *
     * @param string $definition
     * @param string[] $names
     * @return InvalidRoute
     */
    public static function ambiguousAttribute(string $definition, array $names): self
    {
        $names = array_map(function ($name) {
            return "'$name'";
        }, $names);

        $names = implode(", ", $names);

        return new self("Multiple attributes with name $names:\n$definition");
    }

    /**
     * Invalid route definition with unexpected end.
     *
     * @param Input $definition
     * @return InvalidRoute
     */
    public static function unexpectedEnd(Input $definition): self
    {
        $pointer = self::createPointerString($definition->getInput(), $definition->getIndex());

        return new self("Unexpected end of route:\n$pointer");
    }

    /**
     * Invalid route definition with unexpected character.
     *
     * @param Input $definition
     * @param string[] $expected
     * @return InvalidRoute
     */
    public static function unexpectedCharacter(Input $definition, array $expected = []): self
    {
        $pointer = self::createPointerString($definition->getInput(), $definition->getIndex());

        if (empty($expected)) {
            return new self("Unexpected character:\n$pointer");
        }

        $expected = $expected ?? $definition->getLatestExpectations();

        $expected = array_map(function (string $e) {
            return "'$e'";
        }, $expected);

        $expected = implode(", ", $expected);

        return new self("Unexpected character (expected $expected):\n$pointer");
    }

    /**
     * Invalid route definition where optional part is not at the very end.
     *
     * @param Input $definition
     * @return InvalidRoute
     */
    public static function optionalIsNotSuffix(Input $definition): self
    {
        $pointer = self::createPointerString($definition->getInput(), $definition->getIndex());

        return new self("Optional sequence cannot be followed by anything else:\n$pointer");
    }

    /**
     * Invalid route definition with empty attribute name.
     *
     * @param Input $definition
     * @return InvalidRoute
     */
    public static function emptyAttributeName(Input $definition): self
    {
        return self::emptyAttributeParameter($definition, "name");
    }

    /**
     * Invalid route definition with too long attribute name.
     *
     * @param Input $definition
     * @param int $maxLength
     * @return InvalidRoute
     */
    public static function longAttributeName(Input $definition, int $maxLength): self
    {
        return self::longAttributeParameter($definition, $maxLength, "name");
    }

    /**
     * Invalid route definition with empty attribute type.
     *
     * @param Input $definition
     * @return InvalidRoute
     */
    public static function emptyAttributeType(Input $definition): self
    {
        return self::emptyAttributeParameter($definition, "type");
    }

    /**
     * Invalid route definition with too long attribute type.
     *
     * @param Input $definition
     * @param int $maxLength
     * @return InvalidRoute
     */
    public static function longAttributeType(Input $definition, int $maxLength): self
    {
        return self::longAttributeParameter($definition, $maxLength, "type");
    }

    /**
     * Invalid route definition with invalid attribute parameter.
     *
     * @param Input $definition
     * @param string $parameter
     * @return InvalidRoute
     */
    private static function emptyAttributeParameter(Input $definition, string $parameter): self
    {
        $pointer = self::createPointerString($definition->getInput(), $definition->getIndex());

        return new self("The attribute $parameter is missing:\n$pointer");
    }

    /**
     * Invalid route definition with too long attribute parameter.
     *
     * @param Input $definition
     * @param int $maxLength
     * @param string $parameter
     * @return InvalidRoute
     */
    private static function longAttributeParameter(Input $definition, int $maxLength, string $parameter): self
    {
        $pointer = self::createPointerString($definition->getInput(), $definition->getIndex() - 1);

        return new self("The attribute $parameter exceeded maximum allowed length of $maxLength characters:\n$pointer");
    }

    /**
     * Creates a string with an arrow pointing to the character after padding.
     *
     * @param string $string
     * @param int $padding
     * @return string
     */
    private static function createPointerString(string $string, int $padding): string
    {
        $pointer = str_repeat(" ", $padding) . "^";

        return $string . "\n" . $pointer;
    }
}
