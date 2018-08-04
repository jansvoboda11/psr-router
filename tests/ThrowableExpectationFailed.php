<?php

declare(strict_types=1);

namespace SvobodaTest\Router;

use Exception;
use PHPUnit\Framework\ExpectationFailedException;
use SebastianBergmann\Comparator\ComparisonFailure;

/**
 * Wrapper for failed expectation that allows injecting custom location of the expectation failure.
 */
class ThrowableExpectationFailed extends ExpectationFailedException
{
    /**
     * Constructor.
     *
     * @param array $expectationFrame
     * @param string $message
     * @param null|ComparisonFailure $comparisonFailure
     * @param null|Exception $previous
     */
    public function __construct(
        array $expectationFrame,
        string $message = "",
        ?ComparisonFailure $comparisonFailure = null,
        Exception $previous = null
    ) {
        parent::__construct($message, $comparisonFailure, $previous);

        $this->line = $expectationFrame["line"];
        $this->file = $expectationFrame["file"];
    }

    /**
     * Returns the stack trace without extra trace record from ThrowableExpectations trait.
     *
     * @return array
     */
    public function getSerializableTrace(): array
    {
        $trace = $this->getTrace();

        array_shift($trace);

        return $trace;
    }
}
