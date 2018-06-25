<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Parser;

/**
 * Provides parser-friendly methods around given string.
 */
class Input
{
    /**
     * Character representing the end of the input.
     *
     * @var string
     */
    public const END = ";";

    /**
     * @var string
     */
    private $input;

    /**
     * @var int
     */
    private $index;

    /**
     * The last character that was successfully taken from the input.
     *
     * @var null|string
     */
    private $lastTaken;

    /**
     * Characters that were expected in the last call with specified expectations.
     *
     * @var string[]
     */
    private $latestExpectations;

    /**
     * @param string $input
     */
    public function __construct(string $input)
    {
        $this->input = $input;
        $this->index = 0;
        $this->lastTaken = null;
        $this->latestExpectations = [];
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
            $this->lastTaken = self::END;

            return self::END;
        }

        $char = $this->input[$this->index];

        $this->index += 1;

        $this->lastTaken = $char;

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
        $this->latestExpectations = [$char];

        $taken = $this->take();

        if ($taken !== $char) {
            $this->lastTaken = null;

            throw new UnexpectedCharacter($this, [$char]);
        }
    }

    /**
     * Returns a string from the front of the input that consists of characters
     * in the allowed set. Removes the string from the input as well.
     *
     * @param string $ends
     * @return string
     * @throws UnexpectedCharacter
     */
    public function takeAllAlphaNumUntil(string $ends): string
    {
        $ends = str_split($ends);

        $ends[] = "alphanumeric";

        $taken = "";

        while (ctype_alnum($this->peek())) {
            $char = $this->take();

            $taken .= $char;
        }

        $this->latestExpectations = $ends;

        if (!in_array($this->peek(), $ends)) {
            throw new UnexpectedCharacter($this, $ends);
        }

        return $taken;
    }

    /**
     * Returns a string from the front of the input that does not contain the
     * characters in the banned set. Removes the string from the input as well.
     *
     * @param string $ends
     * @return string
     */
    public function takeAllUntil(string $ends): string
    {
        $ends = str_split($ends);

        $ends[] = self::END;

        $taken = "";

        while (!in_array($this->peek(), $ends)) {
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

    /**
     * Returns the last character that was successfully taken.
     *
     * @return null|string
     */
    public function getLastTaken(): ?string
    {
        return $this->lastTaken;
    }

    /**
     * Returns the characters that were expected in the last call.
     *
     * @return string[]
     */
    public function getLatestExpectations(): array
    {
        return $this->latestExpectations;
    }
}
