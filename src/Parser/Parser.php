<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Parser;

use Svoboda\PsrRouter\Route\InvalidRoute;
use Svoboda\PsrRouter\Route\Parts\AttributePart;
use Svoboda\PsrRouter\Route\Parts\EmptyPart;
use Svoboda\PsrRouter\Route\Parts\MainPart;
use Svoboda\PsrRouter\Route\Parts\OptionalPart;
use Svoboda\PsrRouter\Route\Parts\RoutePart;
use Svoboda\PsrRouter\Route\Parts\StaticPart;

/**
 * Parses the user-defined route.
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
     * Parse the route path specification.
     *
     * @param string $path
     * @return RoutePart
     * @throws InvalidRoute
     */
    public function parse(string $path): RoutePart
    {
        $path = new Input($path);

        try {
            $part = $this->parseMain($path);
        } catch (UnexpectedCharacter $exception) {
            if ($path->peek() === Input::END) {
                throw InvalidRoute::unexpectedEnd($path);
            }

            throw InvalidRoute::unexpectedCharacter($path, $exception->getExpected());
        }

        if (!$path->atEnd()) {
            if ($path->getLastTaken() === "]" && $path->peek() !== "]") {
                throw InvalidRoute::optionalIsNotSuffix($path);
            }

            throw InvalidRoute::unexpectedCharacter($path);
        }

        return $part;
    }

    /**
     * Parse the main part of the route specification.
     *
     * @param Input $path
     * @return MainPart
     * @throws InvalidRoute
     * @throws UnexpectedCharacter
     */
    private function parseMain(Input $path): MainPart
    {
        $static = $this->parseStatic($path);

        $attributes = $this->parseAttributes($path);

        $char = $path->peek();

        if ($char === "}") {
            throw InvalidRoute::unexpectedCharacter($path);
        }

        if ($char === "[") {
            $next = $this->parseOptional($path);

            return new MainPart($static, $attributes, $next);
        }

        if ($char === "]" || $char === Input::END) {
            $next = new EmptyPart();

            return new MainPart($static, $attributes, $next);
        }

        $next = $this->parseMain($path);

        return new MainPart($static, $attributes, $next);
    }

    /**
     * Parse the static part of the route specification.
     *
     * @param Input $path
     * @return StaticPart
     */
    private function parseStatic(Input $path): StaticPart
    {
        $static = $path->takeAllUntil("{}[]");

        return new StaticPart($static);
    }

    /**
     * Parse attributes of the route specification.
     *
     * @param Input $path
     * @return AttributePart[]
     * @throws InvalidRoute
     * @throws UnexpectedCharacter
     */
    private function parseAttributes(Input $path): array
    {
        $attributes = [];

        while ($path->peek() === "{") {
            $attributes[] = $this->parseAttribute($path);
        }

        return $attributes;
    }

    /**
     * Parse a single attribute of the route specification.
     *
     * @param Input $path
     * @return AttributePart
     * @throws InvalidRoute
     * @throws UnexpectedCharacter
     */
    private function parseAttribute(Input $path): AttributePart
    {
        $path->expect("{");

        $name = $this->parseAttributeName($path);

        $type = $this->parseAttributeType($path);

        $path->expect("}");

        return new AttributePart($name, $type);
    }

    /**
     * Parse an optional part of the route specification.
     *
     * @param Input $path
     * @return OptionalPart
     * @throws InvalidRoute
     * @throws UnexpectedCharacter
     */
    private function parseOptional(Input $path): OptionalPart
    {
        $path->expect("[");

        $optional = $this->parseMain($path);

        $path->expect("]");

        return new OptionalPart($optional);
    }

    /**
     * Parse the attribute name.
     *
     * @param Input $path
     * @return string
     * @throws InvalidRoute
     * @throws UnexpectedCharacter
     */
    private function parseAttributeName(Input $path): string
    {
        $name = $path->takeAllAlphaNumUntil(":}");

        if (empty($name)) {
            throw InvalidRoute::emptyAttributeName($path);
        }

        if (strlen($name) > self::MAX_ATTRIBUTE_NAME_LENGTH) {
            throw InvalidRoute::tooLongAttributeName($path, self::MAX_ATTRIBUTE_NAME_LENGTH);
        }

        return $name;
    }

    /**
     * Parse the attribute type.
     *
     * @param Input $path
     * @return null|string
     * @throws InvalidRoute
     * @throws UnexpectedCharacter
     */
    private function parseAttributeType(Input $path): ?string
    {
        if ($path->peek() !== ":") {
            return null;
        }

        $path->take();

        $type = $path->takeAllAlphaNumUntil("}");

        if (empty($type)) {
            throw InvalidRoute::emptyAttributeType($path);
        }

        if (strlen($type) > self::MAX_ATTRIBUTE_TYPE_LENGTH) {
            throw InvalidRoute::tooLongAttributeType($path, self::MAX_ATTRIBUTE_TYPE_LENGTH);
        }

        return $type;
    }
}
