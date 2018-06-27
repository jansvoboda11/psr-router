<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Parser;

use Svoboda\PsrRouter\Route\InvalidRoute;
use Svoboda\PsrRouter\Route\Path\AttributePath;
use Svoboda\PsrRouter\Route\Path\EmptyPath;
use Svoboda\PsrRouter\Route\Path\MainPath;
use Svoboda\PsrRouter\Route\Path\OptionalPath;
use Svoboda\PsrRouter\Route\Path\RoutePath;
use Svoboda\PsrRouter\Route\Path\StaticPath;

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
            $parsed = $this->parseMain($definition);
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
     * @return MainPath
     * @throws InvalidRoute
     * @throws UnexpectedChar
     */
    private function parseMain(Input $definition): MainPath
    {
        $static = $this->parseStatic($definition);

        $attributes = $this->parseAttributes($definition);

        $char = $definition->peek();

        if ($char === "}") {
            throw InvalidRoute::unexpectedCharacter($definition);
        }

        if ($char === "[") {
            $next = $this->parseOptional($definition);

            return new MainPath($static, $attributes, $next);
        }

        if ($char === "]" || $char === Input::END) {
            $next = new EmptyPath();

            return new MainPath($static, $attributes, $next);
        }

        $next = $this->parseMain($definition);

        return new MainPath($static, $attributes, $next);
    }

    /**
     * Parse the static part of the route definition.
     *
     * @param Input $definition
     * @return StaticPath
     */
    private function parseStatic(Input $definition): StaticPath
    {
        $static = $definition->takeAllUntil("{}[]");

        return new StaticPath($static);
    }

    /**
     * Parse attributes of the route definition.
     *
     * @param Input $definition
     * @return AttributePath[]
     * @throws InvalidRoute
     * @throws UnexpectedChar
     */
    private function parseAttributes(Input $definition): array
    {
        $attributes = [];

        while ($definition->peek() === "{") {
            $attributes[] = $this->parseAttribute($definition);
        }

        return $attributes;
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

        return new AttributePath($name, $type);
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

        $optional = $this->parseMain($definition);

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
