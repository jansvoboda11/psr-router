<?php

declare(strict_types=1);

namespace Svoboda\Router\Parser;

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
     * The underlying string input.
     *
     * @var string
     */
    private $input;

    /**
     * Index of the next character.
     *
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
     * Constructor.
     *
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
     * Removes the next character from the input. Fails if it's not the
     * expected one.
     *
     * @param string $char
     * @throws UnexpectedChar
     */
    public function expect(string $char): void
    {
        $this->latestExpectations = [$char];

        $taken = $this->take();

        if ($taken !== $char) {
            $this->lastTaken = null;

            throw new UnexpectedChar($this, [$char]);
        }
    }

    /**
     * Removes all alphanumeric characters from the input until one of the end
     * characters is encountered. Returns the removed alphanumeric string.
     *
     * @param string $ends
     * @return string
     * @throws UnexpectedChar
     */
    public function takeAllAlphaNumUntil(string $ends): string
    {
        $ends = self::split($ends);

        $ends[] = "alphanumeric";

        $taken = "";

        while (ctype_alnum($this->peek())) {
            $char = $this->take();

            $taken .= $char;
        }

        $this->latestExpectations = $ends;

        if (!in_array($this->peek(), $ends)) {
            throw new UnexpectedChar($this, $ends);
        }

        return $taken;
    }

    /**
     * Removes all characters from the input until one of the end characters is
     * encountered. Returns the read removed.
     *
     * @param string $ends
     * @return string
     */
    public function takeAllUntil(string $ends): string
    {
        $ends = self::split($ends);

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
     * Returns the original input.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->input;
    }

    /**
     * Returns index of the next character.
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

    /**
     * Splits the string into array of characters. Type-sane version of
     * str_split for PHPStan.
     *
     * @param string $string
     * @return string[]
     */
    private static function split(string $string): array
    {
        $chars = [];

        for ($i = 0; $i < strlen($string); $i++) {
            $chars[] = $string[$i];
        }

        return $chars;
    }
}
