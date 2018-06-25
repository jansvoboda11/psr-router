<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Parser;

/**
 * Provides parser-friendly methods around given string.
 */
class Input
{
    /**
     * Symbol representing the end of the input.
     *
     * @var string
     */
    public const END = "%";

    /**
     * @var string[]
     */
    private $input;

    /**
     * @param string $input
     */
    public function __construct(string $input)
    {
        $this->input = empty($input) ? [] : str_split($input);
    }

    /**
     * Returns the next character without removing it from the input.
     *
     * @return string
     */
    public function peek(): string
    {
        if ($this->atEnd()) {
            return self::END;
        }

        return $this->input[0];
    }

    /**
     * Returns the next character and removes it from the input.
     *
     * @return string
     */
    public function take(): string
    {
        if ($this->atEnd()) {
            return self::END;
        }

        return array_shift($this->input);
    }

    /**
     * Removes the specified character from the input. Fails if it does not
     * match the first character in the input.
     *
     * @param string $chars
     * @throws UnexpectedCharacter
     */
    public function expect(string $chars): void
    {
        $expected = str_split($chars);

        $taken = $this->take();

        if (!in_array($taken, $expected)) {
            throw new UnexpectedCharacter();
        }
    }

    /**
     * Returns a string from the front of the input that consists of characters
     * in the allowed set. Removes the string from the input as well.
     *
     * @param string $allowed
     * @return string
     */
    public function takeAllWhile(string $allowed): string
    {
        $allowed = str_split($allowed);

        $taken = "";

        while (in_array($this->peek(), $allowed)) {
            $char = $this->take();

            $taken .= $char;
        }

        return $taken;
    }

    /**
     * Returns a string from the front of the input that does not contain the
     * characters in the banned set. Removes the string from the input as well.
     *
     * @param string $banned
     * @return string
     */
    public function takeAllUntil(string $banned): string
    {
        $banned = str_split($banned);

        $banned[] = self::END;

        $taken = "";

        while (!in_array($this->peek(), $banned)) {
            $char = $this->take();

            $taken .= $char;
        }

        return $taken;
    }

    /**
     * Determines if the end of input was reached.
     *
     * @return bool
     */
    public function atEnd(): bool
    {
        return empty($this->input);
    }
}
