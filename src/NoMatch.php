<?php

declare(strict_types=1);

namespace Svoboda\Router;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Exception representing routing failure - no exact route match.
 */
class NoMatch extends Exception
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
}
