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
use function array_shift;
use function in_array;
use function is_null;
use function str_split;
use function strlen;

/**
 * Parses the user-defined route.
 */
class Parser
{
    /**
     * Character returned when the end of route definition is encountered.
     *
     * @string
     */
    private const EOF = "%";

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

        $ast = $this->parsePath(str_split($route->getPath()));

        $handlerName = $route->getHandlerName();

        return new ParsedRoute($method, $ast, $handlerName);
    }

    /**
     * Parse the route path specification.
     *
     * @param string[] $path
     * @return RoutePart
     * @throws InvalidRoute
     */
    private function parsePath(array $path): RoutePart
    {
        $part = $this->parseMain($path);

        if (!empty($path)) {
            throw new InvalidRoute();
        }

        return $part;
    }

    /**
     * Parse the main part of the route specification.
     *
     * @param string[] $path
     * @return MainPart
     * @throws InvalidRoute
     */
    private function parseMain(array &$path): MainPart
    {
        $static = $this->parseStatic($path);

        $attributes = $this->parseAttributes($path);

        $char = $this->peek($path, true);

        if ($char === "}") {
            throw new InvalidRoute();
        }

        if ($char === "[") {
            $next = $this->parseOptional($path);

            return new MainPart($static, $attributes, $next);
        }

        if ($char === "]" || $char === self::EOF) {
            $next = new EmptyPart();

            return new MainPart($static, $attributes, $next);
        }

        $next = $this->parseMain($path);

        return new MainPart($static, $attributes, $next);
    }

    /**
     * Parse the static part of the route specification.
     *
     * @param string[] $path
     * @return StaticPart
     * @throws InvalidRoute
     */
    private function parseStatic(array &$path): StaticPart
    {
        $eof = self::EOF;

        $static = $this->takeAllUntil("{}[]$eof", $path);

        return new StaticPart($static);
    }

    /**
     * Parse attributes of the route specification.
     *
     * @param string[] $path
     * @return AttributePart[]
     * @throws InvalidRoute
     */
    private function parseAttributes(array &$path): array
    {
        $attributes = [];

        while ($this->peek($path, true) === "{") {
            $attributes[] = $this->parseAttribute($path);
        }

        return $attributes;
    }

    /**
     * Parse a single attribute of the route specification.
     *
     * @param string[] $path
     * @return AttributePart
     * @throws InvalidRoute
     */
    private function parseAttribute(array &$path): AttributePart
    {
        $this->expect("{", $path);

        $name = $this->parseAttributeName($path);

        $type = $this->parseAttributeType($path);

        $this->expect("}", $path);

        return new AttributePart($name, $type);
    }

    /**
     * Parse an optional part of the route specification.
     *
     * @param string[] $path
     * @return OptionalPart
     * @throws InvalidRoute
     */
    private function parseOptional(array &$path): OptionalPart
    {
        $this->expect("[", $path);

        $optional = $this->parseMain($path);

        $this->expect("]", $path);

        return new OptionalPart($optional);
    }

    /**
     * Parse the attribute name.
     *
     * @param string[] $path
     * @return string
     * @throws InvalidRoute
     */
    private function parseAttributeName(array &$path): string
    {
        $name = $this->takeAllIn(self::ALPHA_NUMERIC, $path);

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
     * @param string[] $path
     * @return null|string
     * @throws InvalidRoute
     */
    private function parseAttributeType(array &$path): ?string
    {
        if ($this->peek($path) !== ":") {
            return null;
        }

        $this->take($path);

        $type = $this->takeAllIn(self::ALPHA_NUMERIC, $path);

        if (empty($type)) {
            throw new InvalidRoute();
        }

        if (strlen($type) > self::MAX_ATTRIBUTE_TYPE_LENGTH) {
            throw new InvalidRoute();
        }

        return $type;
    }

    /**
     * Returns the next character without removing it from the input.
     *
     * @param string[] $input
     * @param bool $canBeEof
     * @return string
     * @throws InvalidRoute
     */
    private function peek(array $input, bool $canBeEof = false): string
    {
        if (!empty($input)) {
            return $input[0];
        }

        if (!$canBeEof) {
            throw new InvalidRoute();
        }

        return self::EOF;
    }

    /**
     * Returns the next character and removes it from the input.
     *
     * @param string[] $input
     * @return string
     * @throws InvalidRoute
     */
    private function take(array &$input): string
    {
        $char = array_shift($input);

        if (is_null($char)) {
            throw new InvalidRoute();
        }

        return $char;
    }

    /**
     * Removes the specified character from the input. Fails if it does not match the first character in the input.
     *
     * @param string $chars
     * @param string[] $input
     * @throws InvalidRoute
     */
    private function expect(string $chars, array &$input): void
    {
        $taken = $this->take($input);

        if (!in_array($taken, str_split($chars))) {
            throw new InvalidRoute();
        }
    }

    /**
     * Returns a string from the front of the input that consists of characters
     * in the allowed set. Removes the string from the input as well.
     *
     * @param string $allowed
     * @param string[] $input
     * @return string
     * @throws InvalidRoute
     */
    private function takeAllIn(string $allowed, array &$input): string
    {
        $allowed = str_split($allowed);

        $taken = "";

        while (in_array($this->peek($input), $allowed)) {
            $char = $this->take($input);

            $taken .= $char;
        }

        return $taken;
    }

    /**
     * Returns a string from the front of the input that does not contain the
     * characters in the banned set. Removes the string from the input as well.
     *
     * @param string $banned
     * @param string[] $input
     * @return string
     * @throws InvalidRoute
     */
    private function takeAllUntil(string $banned, array &$input): string
    {
        $banned = str_split($banned);

        $canBeEof = in_array(self::EOF, $banned);

        $taken = "";

        while (!in_array($this->peek($input, $canBeEof), $banned)) {
            $char = $this->take($input);

            $taken .= $char;
        }

        return $taken;
    }
}
