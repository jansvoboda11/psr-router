<?php

declare(strict_types=1);

namespace Svoboda\Router\Parser;

use Svoboda\Router\Route\InvalidRoute;
use Svoboda\Router\Route\Path\AttributePath;
use Svoboda\Router\Route\Path\EmptyPath;
use Svoboda\Router\Route\Path\OptionalPath;
use Svoboda\Router\Route\Path\RoutePath;
use Svoboda\Router\Route\Path\StaticPath;
use Svoboda\Router\Types\TypeCollection;

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
     * @param TypeCollection $types
     * @return RoutePath
     * @throws InvalidRoute
     */
    public function parse(string $definition, TypeCollection $types): RoutePath
    {
        $definition = new Input($definition);

        try {
            $parsed = $this->parseRoute($definition, $types, []);
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
     * @param TypeCollection $types
     * @param string[] $attributes
     * @return RoutePath
     * @throws InvalidRoute
     * @throws UnexpectedChar
     */
    private function parseRoute(Input $definition, TypeCollection $types, array $attributes): RoutePath
    {
        $char = $definition->peek();

        if ($char === "{") {
            return $this->parseAttribute($definition, $types, $attributes);
        }

        if ($char === "[") {
            return $this->parseOptional($definition, $types, $attributes);
        }

        if ($char === "}") {
            throw InvalidRoute::unexpectedCharacter($definition, $attributes);
        }

        if ($char === "]" || $char === Input::END) {
            return new EmptyPath();
        }

        return $this->parseStatic($definition, $types, $attributes);
    }

    /**
     * Parse the static part of the route definition.
     *
     * @param Input $definition
     * @param TypeCollection $types
     * @param string[] $attributes
     * @return StaticPath
     * @throws InvalidRoute
     * @throws UnexpectedChar
     */
    private function parseStatic(Input $definition, TypeCollection $types, array $attributes): StaticPath
    {
        $static = $definition->takeAllUntil("{}[]");

        $next = $this->parseRoute($definition, $types, $attributes);

        return new StaticPath($static, $next);
    }

    /**
     * Parse a single attribute of the route definition.
     *
     * @param Input $definition
     * @param TypeCollection $types
     * @param string[] $attributes
     * @return AttributePath
     * @throws InvalidRoute
     * @throws UnexpectedChar
     */
    private function parseAttribute(Input $definition, TypeCollection $types, array $attributes): AttributePath
    {
        $definition->expect("{");

        $name = $this->parseAttributeName($definition);
        $type = $this->parseAttributeType($definition);

        $definition->expect("}");

        if (in_array($name, $attributes)) {
            throw InvalidRoute::ambiguousAttribute($definition, $name);
        }

        if ($type !== null && !$types->hasNamed($type)) {
            throw InvalidRoute::unknownAttributeType($definition, $name, $type);
        }

        $attributes[] = $name;

        $next = $this->parseRoute($definition, $types, $attributes);

        return new AttributePath($name, $type, $types, $next);
    }

    /**
     * Parse the optional part of the route definition.
     *
     * @param Input $definition
     * @param TypeCollection $types
     * @param string[] $attributes
     * @return OptionalPath
     * @throws InvalidRoute
     * @throws UnexpectedChar
     */
    private function parseOptional(Input $definition, TypeCollection $types, array $attributes): OptionalPath
    {
        $definition->expect("[");

        $optional = $this->parseRoute($definition, $types, $attributes);

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
