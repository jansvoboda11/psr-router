<?php

declare(strict_types=1);

namespace SvobodaTest\Router;

use PHPUnit\Framework\Assert;
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
     * @var null|Throwable
     */
    private $throwable;

    /**
     * The frame where the expectation was set.
     *
     * @var null|array
     */
    private $expectationFrame;

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
        $this->expectationFrame = null;
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
        $this->expectationFrame = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
    }

    /**
     * Black magic. Should be called in the `runTest` method of the test case:
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
        } catch (\PHPUnit\Framework\Exception $exception) {
            // propagate any native failures and no-throw failure from above
            throw $exception;
        } catch (Throwable $actual) {
            if (!$this->shouldHaveThrown()) {
                throw $actual;
            }

            try {
                Assert::assertEquals($this->throwable, $actual);
            } catch (ExpectationFailedException $error) {
                throw $this->createThrowableComparisonFailure($error);
            }
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
            $this->expectationFrame,
            "Failed asserting that a throwable object was thrown."
        );
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
            $this->expectationFrame,
            "Failed asserting that two throwable objects are equal.",
            $error->getComparisonFailure(),
            $error->getPrevious()
        );
    }
}
