<?php

declare(strict_types=1);

namespace Svoboda\Router\Parser;

use Svoboda\Router\Exception;

/**
 * Unexpected character in the input.
 */
class UnexpectedChar extends Exception
{
    /**
     * The input with unexpected character.
     *
     * @var Input
     */
    private $input;

    /**
     * Characters that were expected instead.
     *
     * @var string[]
     */
    private $expected;

    /**
     * Constructor.
     *
     * @param Input $input
     * @param string[] $expected
     */
    public function __construct(Input $input, array $expected)
    {
        parent::__construct("Encountered an unexpected character");

        $this->input = $input;
        $this->expected = $expected;
    }

    /**
     * Returns the input.
     *
     * @return Input
     */
    public function getInput(): Input
    {
        return $this->input;
    }

    /**
     * Returns the expected characters.
     *
     * @return string[]
     */
    public function getExpected(): array
    {
        return $this->expected;
    }
}
