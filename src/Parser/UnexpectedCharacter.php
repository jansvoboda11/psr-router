<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Parser;

use Svoboda\PsrRouter\PsrRouterException;

/**
 * Unexpected character in the input.
 */
class UnexpectedCharacter extends PsrRouterException
{
    /**
     * @var Input
     */
    private $input;

    /**
     * @var string[]
     */
    private $expected;

    /**
     * @param Input $input
     * @param string[] $expected
     */
    public function __construct(Input $input, array $expected)
    {
        $this->input = $input;
        $this->expected = $expected;

        parent::__construct();
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
