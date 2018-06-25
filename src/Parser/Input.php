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
     * @var string
     */
    private $input;

    /**
     * @var int
     */
    private $index;

    /**
     * @param string $input
     */
    public function __construct(string $input)
    {
        $this->input = $input;
        $this->index = 0;
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

        return $this->input[$this->index];
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

        $char = $this->input[$this->index];

        $this->index += 1;

        return $char;
    }

    /**
     * Removes the specified character from the input. Fails if it does not
     * match the first character in the input.
     *
     * @param string $char
     * @throws UnexpectedCharacter
     */
    public function expect(string $char): void
    {
        $taken = $this->take();

        if ($taken !== $char) {
            throw new UnexpectedCharacter($this->input, $this->index, $char);
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
        return $this->index === strlen($this->input);
    }

    /**
     * Returns the original input.
     *
     * @return string
     */
    public function getInput(): string
    {
        return $this->input;
    }

    /**
     * Returns index of the current character.
     *
     * @return int
     */
    public function getIndex(): int
    {
        return $this->index;
    }
}
