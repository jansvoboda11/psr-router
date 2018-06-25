<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Parser;

use Svoboda\PsrRouter\InvalidRoute;
use Svoboda\PsrRouter\Parser\Parts\AttributePart;
use Svoboda\PsrRouter\Parser\Parts\EmptyPart;
use Svoboda\PsrRouter\Parser\Parts\MainPart;
use Svoboda\PsrRouter\Parser\Parts\OptionalPart;
use Svoboda\PsrRouter\Parser\Parts\RoutePart;
use Svoboda\PsrRouter\Parser\Parts\StaticPart;
use Svoboda\PsrRouter\Route;
use function strlen;

/**
 * Parses the user-defined route.
 */
class Parser
{
    /**
     * Alphanumeric characters.
     *
     * @string
     */
    private const ALPHA_NUMERIC = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUV01234567890";

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
     * Parse the given route.
     *
     * @param Route $route
     * @return ParsedRoute
     * @throws InvalidRoute
     */
    public function parse(Route $route): ParsedRoute
    {
        $method = $route->getMethod();

        $path = new Input($route->getPath());

        $ast = $this->parsePath($path);

        $handlerName = $route->getHandlerName();

        return new ParsedRoute($method, $ast, $handlerName);
    }

    /**
     * Parse the route path specification.
     *
     * @param Input $path
     * @return RoutePart
     * @throws InvalidRoute
     */
    private function parsePath(Input $path): RoutePart
    {
        $part = $this->parseMain($path);

        if (!$path->atEnd()) {
            throw new InvalidRoute();
        }

        return $part;
    }

    /**
     * Parse the main part of the route specification.
     *
     * @param Input $path
     * @return MainPart
     * @throws InvalidRoute
     */
    private function parseMain(Input $path): MainPart
    {
        $static = $this->parseStatic($path);

        $attributes = $this->parseAttributes($path);

        $char = $path->peek(true);

        if ($char === "}") {
            throw new InvalidRoute();
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
     * @throws InvalidRoute
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
     */
    private function parseAttributes(Input $path): array
    {
        $attributes = [];

        while ($path->peek(true) === "{") {
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
     */
    private function parseAttributeName(Input $path): string
    {
        $name = $path->takeAllWhile(self::ALPHA_NUMERIC);

        if (empty($name)) {
            throw new InvalidRoute();
        }

        if (strlen($name) > self::MAX_ATTRIBUTE_NAME_LENGTH) {
            throw new InvalidRoute();
        }

        return $name;
    }

    /**
     * Parse the attribute type.
     *
     * @param Input $path
     * @return null|string
     * @throws InvalidRoute
     */
    private function parseAttributeType(Input $path): ?string
    {
        if ($path->peek() !== ":") {
            return null;
        }

        $path->take();

        $type = $path->takeAllWhile(self::ALPHA_NUMERIC);

        if (empty($type)) {
            throw new InvalidRoute();
        }

        if (strlen($type) > self::MAX_ATTRIBUTE_TYPE_LENGTH) {
            throw new InvalidRoute();
        }

        return $type;
    }
}
