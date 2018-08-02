<?php

declare(strict_types=1);

namespace SvobodaTest\Router;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use Throwable;

/**
 * Allows to set expectations for thrown objects. Does a full object comparison instead of only comparing the class,
 * message and code of the expected and actual exceptions (which is the default behaviour of PHPUnit).
 */
trait ThrowableExpectations
{
    /**
     * The expected throwable object.
     *
     * @var Throwable
     */
    private $throwable;

    /**
     * Filename of the test with throwable expectation.
     *
     * @var string
     */
    private $throwableFile;

    /**
     * Line number where the throwable expectation was set.
     *
     * @var int
     */
    private $throwableLine;

    /**
     * Set up the expectations. Should be called in the `setUp` method of test case:
     *
     * protected function setUp()
     * {
     *     $this->setUpThrowableExpectations();
     * }
     */
    protected function setUpThrowableExpectations(): void
    {
        $this->throwable = null;
        $this->throwableFile = null;
        $this->throwableLine = null;
    }

    /**
     * Verify that the following code throws the given object. Should be called in test method:
     *
     * public function test_it_throws()
     * {
     *     $this->expectThrowable(new \Exception("My exception message."));
     *
     *     throw new \Exception("My exception message.");
     * }
     *
     * @param Throwable $throwable
     */
    protected function expectThrowable(Throwable $throwable): void
    {
        $this->throwable = $throwable;

        $caller = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT | DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];

        $this->throwableFile = $caller["file"];
        $this->throwableLine = $caller["line"];
    }

    /**
     * Black magic. Should be called in the `runTest` method of your test case:
     *
     * protected function runTest()
     * {
     *     $this->handleThrowableExpectations(function () {
     *         parent::runTest();
     *     });
     * }
     *
     * @param callable $test
     * @throws Throwable
     */
    protected function handleThrowableExpectations(callable $test): void
    {
        try {
            $test();

            if ($this->shouldHaveThrown()) {
                throw $this->createNoThrowFailure();
            }
        } catch (Exception $exception) {
            // propagate the no throw failure from above
            throw $exception;
        } catch (Throwable $actual) {
            if (!$this->shouldHaveThrown()) {
                // ignore unexpected throwable
                throw $actual;
            }

            $this->handleThrow($actual);
        }
    }

    /**
     * Determine if the test should have thrown.
     *
     * @return bool
     */
    private function shouldHaveThrown(): bool
    {
        return $this->throwable !== null;
    }

    /**
     * Create a new exception that represents the situation when the test should have thrown but did not.
     *
     * @return ThrowableExpectationFailed
     */
    private function createNoThrowFailure(): ThrowableExpectationFailed
    {
        return new ThrowableExpectationFailed(
            $this->throwableFile,
            $this->throwableLine,
            "Failed asserting that a throwable object was thrown."
        );
    }

    /**
     * Handle the situation when test did throw.
     *
     * @param Throwable $actual
     * @throws Throwable
     */
    private function handleThrow(Throwable $actual): void
    {
        try {
            Assert::assertEquals($this->throwable, $actual);
        } catch (ExpectationFailedException $error) {
            throw $this->createThrowableComparisonFailure($error);
        }
    }

    /**
     * Create a new exception that represents the situation when the test threw a different object than the expected.
     *
     * @param ExpectationFailedException $error
     * @return ThrowableExpectationFailed
     */
    private function createThrowableComparisonFailure(ExpectationFailedException $error): ThrowableExpectationFailed
    {
        return new ThrowableExpectationFailed(
            $this->throwableFile,
            $this->throwableLine,
            "Failed asserting that two throwable objects are equal.",
            $error->getComparisonFailure(),
            $error->getPrevious()
        );
    }
}
