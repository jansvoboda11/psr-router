<?php

declare(strict_types=1);

namespace Svoboda\Router;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Exception representing routing failure - no exact route match.
 */
class Failure extends Exception
{
    /**
     * Allowed methods for the request URI.
     *
     * @var string[]
     */
    private $allowedMethods;

    /**
     * The original request.
     *
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * Constructor.
     *
     * @param string[] $allowedMethods
     * @param ServerRequestInterface $request
     */
    public function __construct(array $allowedMethods, ServerRequestInterface $request)
    {
        parent::__construct("Failed to match incoming request.");

        $this->allowedMethods = $allowedMethods;
        $this->request = $request;
    }

    /**
     * Returns the allowed methods.
     *
     * @return string[]
     */
    public function getAllowedMethods(): array
    {
        return $this->allowedMethods;
    }

    /**
     * Returns the original request.
     *
     * @return ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    /**
     * Determines whether the failure was caused by an incorrect HTTP method.
     *
     * @return bool
     */
    public function isMethodFailure(): bool
    {
        return !empty($this->allowedMethods);
    }
}
