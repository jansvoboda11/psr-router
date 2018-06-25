<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Parser;

use Svoboda\PsrRouter\PsrRouterException;

class UnexpectedCharacter extends PsrRouterException
{
    /**
     * @var string
     */
    private $input;

    /**
     * @var int
     */
    private $index;

    /**
     * @var string
     */
    private $expected;

    /**
     * @param string $input
     * @param int $index
     * @param string $expected
     */
    public function __construct(string $input, int $index, string $expected)
    {
        $this->input = $input;
        $this->index = $index;
        $this->expected = $expected;
    }
}
