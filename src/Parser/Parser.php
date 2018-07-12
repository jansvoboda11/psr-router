<?php

declare(strict_types=1);

namespace Svoboda\Router\Parser;

use Svoboda\Router\Route\InvalidRoute;
use Svoboda\Router\Route\Path\AttributePath;
use Svoboda\Router\Route\Path\EmptyPath;
use Svoboda\Router\Route\Path\OptionalPath;
use Svoboda\Router\Route\Path\RoutePath;
use Svoboda\Router\Route\Path\StaticPath;

/**
 * Parses route definitions.
 */
class Parser
{
    /**
     * Maximum allowed length of attribute name.
     *
     * @int
     */
    private const MAX_ATTRIBUTE_NAME_LENGTH = 32;

    /**
     * Maximum allowed length of attribute type.
     *
     * @int
     */
    private const MAX_ATTRIBUTE_TYPE_LENGTH = 32;

    /**
     * Parse the route path definition.
     *
     * @param string $definition
     * @return RoutePath
     * @throws InvalidRoute
     */
    public function parse(string $definition): RoutePath
    {
        $definition = new Input($definition);

        try {
            $parsed = $this->parseRoute($definition);
        } catch (UnexpectedChar $exception) {
            if ($definition->peek() === Input::END) {
                throw InvalidRoute::unexpectedEnd($definition);
            }

            $expected = $exception->getExpected();

            throw InvalidRoute::unexpectedCharacter($definition, $expected);
        }

        if (!$definition->atEnd()) {
            if ($definition->getLastTaken() === "]" && $definition->peek() !== "]") {
                throw InvalidRoute::optionalIsNotSuffix($definition);
            }

            throw InvalidRoute::unexpectedCharacter($definition);
        }

        return $parsed;
    }

    /**
     * Parse the main part of the route definition.
     *
     * @param Input $definition
     * @return RoutePath
     * @throws InvalidRoute
     * @throws UnexpectedChar
     */
    private function parseRoute(Input $definition): RoutePath
    {
        $char = $definition->peek();

        if ($char === "{") {
            return $this->parseAttribute($definition);
        }

        if ($char === "[") {
            return $this->parseOptional($definition);
        }

        if ($char === "}") {
            throw InvalidRoute::unexpectedCharacter($definition);
        }

        if ($char === "]" || $char === Input::END) {
            return new EmptyPath();
        }

        return $this->parseStatic($definition);
    }

    /**
     * Parse the static part of the route definition.
     *
     * @param Input $definition
     * @return StaticPath
     * @throws InvalidRoute
     * @throws UnexpectedChar
     */
    private function parseStatic(Input $definition): StaticPath
    {
        $static = $definition->takeAllUntil("{}[]");

        $next = $this->parseRoute($definition);

        return new StaticPath($static, $next);
    }

    /**
     * Parse a single attribute of the route definition.
     *
     * @param Input $definition
     * @return AttributePath
     * @throws InvalidRoute
     * @throws UnexpectedChar
     */
    private function parseAttribute(Input $definition): AttributePath
    {
        $definition->expect("{");

        $name = $this->parseAttributeName($definition);
        $type = $this->parseAttributeType($definition);

        $definition->expect("}");

        $next = $this->parseRoute($definition);

        return new AttributePath($name, $type, $next);
    }

    /**
     * Parse the optional part of the route definition.
     *
     * @param Input $definition
     * @return OptionalPath
     * @throws InvalidRoute
     * @throws UnexpectedChar
     */
    private function parseOptional(Input $definition): OptionalPath
    {
        $definition->expect("[");

        $optional = $this->parseRoute($definition);

        $definition->expect("]");

        return new OptionalPath($optional);
    }

    /**
     * Parse the attribute name.
     *
     * @param Input $definition
     * @return string
     * @throws InvalidRoute
     * @throws UnexpectedChar
     */
    private function parseAttributeName(Input $definition): string
    {
        $maxLength = self::MAX_ATTRIBUTE_NAME_LENGTH;

        $name = $definition->takeAllAlphaNumUntil(":}");

        if (empty($name)) {
            throw InvalidRoute::emptyAttributeName($definition);
        }

        if (strlen($name) > $maxLength) {
            throw InvalidRoute::longAttributeName($definition, $maxLength);
        }

        return $name;
    }

    /**
     * Parse the attribute type.
     *
     * @param Input $definition
     * @return null|string
     * @throws InvalidRoute
     * @throws UnexpectedChar
     */
    private function parseAttributeType(Input $definition): ?string
    {
        $maxLength = self::MAX_ATTRIBUTE_TYPE_LENGTH;

        if ($definition->peek() !== ":") {
            return null;
        }

        $definition->take();

        $type = $definition->takeAllAlphaNumUntil("}");

        if (empty($type)) {
            throw InvalidRoute::emptyAttributeType($definition);
        }

        if (strlen($type) > $maxLength) {
            throw InvalidRoute::longAttributeType($definition, $maxLength);
        }

        return $type;
    }
}
