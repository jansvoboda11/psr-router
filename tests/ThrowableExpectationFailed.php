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
     * Name of the file where the throwable expectation was created.
     *
     * @var string
     */
    private $expectationFile;

    /**
     * Number of the line where the throwable expectation was created.
     *
     * @var int
     */
    private $expectationLine;

    /**
     * Constructor.
     *
     * @param string $expectationFile
     * @param int $expectationLine
     * @param string $message
     * @param null|ComparisonFailure $comparisonFailure
     * @param null|Exception $previous
     */
    public function __construct(
        string $expectationFile,
        int $expectationLine,
        string $message = "",
        ?ComparisonFailure $comparisonFailure = null,
        Exception $previous = null
    ) {
        parent::__construct($message, $comparisonFailure, $previous);

        $this->expectationFile = $expectationFile;
        $this->expectationLine = $expectationLine;
    }

    /**
     * Removes extra trace records and injects the position of throwable expectation.
     *
     * @return array
     */
    public function getSerializableTrace(): array
    {
        $trace = parent::getSerializableTrace();

        // remove internal calls in ThrowableExpectations trait
        array_shift($trace);
        array_shift($trace);

        $trace[0]["file"] = $this->expectationFile;
        $trace[0]["line"] = $this->expectationLine;

        return $trace;
    }
}
