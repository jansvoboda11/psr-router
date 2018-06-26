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
     * @return RoutePath
     * @throws InvalidRoute
     */
    public function parse(string $path): RoutePath
    {
        $path = new Input($path);

        try {
            $parsed = $this->parseMain($path);
        } catch (UnexpectedChar $exception) {
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

        return $parsed;
    }

    /**
     * Parse the main part of the route specification.
     *
     * @param Input $path
     * @return MainPath
     * @throws InvalidRoute
     * @throws UnexpectedChar
     */
    private function parseMain(Input $path): MainPath
    {
        $static = $this->parseStatic($path);

        $attributes = $this->parseAttributes($path);

        $char = $path->peek();

        if ($char === "}") {
            throw InvalidRoute::unexpectedCharacter($path);
        }

        if ($char === "[") {
            $next = $this->parseOptional($path);

            return new MainPath($static, $attributes, $next);
        }

        if ($char === "]" || $char === Input::END) {
            $next = new EmptyPath();

            return new MainPath($static, $attributes, $next);
        }

        $next = $this->parseMain($path);

        return new MainPath($static, $attributes, $next);
    }

    /**
     * Parse the static part of the route specification.
     *
     * @param Input $path
     * @return StaticPath
     */
    private function parseStatic(Input $path): StaticPath
    {
        $static = $path->takeAllUntil("{}[]");

        return new StaticPath($static);
    }

    /**
     * Parse attributes of the route specification.
     *
     * @param Input $path
     * @return AttributePath[]
     * @throws InvalidRoute
     * @throws UnexpectedChar
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
     * @return AttributePath
     * @throws InvalidRoute
     * @throws UnexpectedChar
     */
    private function parseAttribute(Input $path): AttributePath
    {
        $path->expect("{");

        $name = $this->parseAttributeName($path);
        $type = $this->parseAttributeType($path);

        $path->expect("}");

        return new AttributePath($name, $type);
    }

    /**
     * Parse the optional part of the route specification.
     *
     * @param Input $path
     * @return OptionalPath
     * @throws InvalidRoute
     * @throws UnexpectedChar
     */
    private function parseOptional(Input $path): OptionalPath
    {
        $path->expect("[");

        $optional = $this->parseMain($path);

        $path->expect("]");

        return new OptionalPath($optional);
    }

    /**
     * Parse the attribute name.
     *
     * @param Input $path
     * @return string
     * @throws InvalidRoute
     * @throws UnexpectedChar
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
     * @throws UnexpectedChar
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
