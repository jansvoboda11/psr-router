<?php

declare(strict_types=1);

namespace Svoboda\Router;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Exception representing routing failure - no exact route match.
 */
class Failure extends Exception
{
    /**
     * Handlers that could handle the URI in a combination with a different HTTP method.
     * Array keys are corresponding HTTP methods.
     *
     * @var RequestHandlerInterface[]
     */
    private $uriHandlers;

    /**
     * The original request.
     *
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * Constructor.
     *
     * @param RequestHandlerInterface[] $uriHandlers
     * @param ServerRequestInterface $request
     */
    public function __construct(array $uriHandlers, ServerRequestInterface $request)
    {
        parent::__construct("Failed to match incoming request");

        $this->uriHandlers = $uriHandlers;
        $this->request = $request;
    }

    /**
     * Returns handlers that could handle the URI in a combination with a different HTTP method.
     * Array keys are their respective HTTP methods.
     *
     * @return RequestHandlerInterface[]
     */
    public function getUriHandlers(): array
    {
        return $this->uriHandlers;
    }

    /**
     * Determines whether the failure was caused by an incorrect HTTP method.
     *
     * @return bool
     */
    public function isMethodFailure(): bool
    {
        return !empty($this->uriHandlers);
    }

    /**
     * Returns the allowed methods.
     *
     * @return string[]
     */
    public function getAllowedMethods(): array
    {
        return array_keys($this->uriHandlers);
    }

    /**
     * Determine if the given method is allowed.
     *
     * @param string $method
     * @return bool
     */
    public function isMethodAllowed(string $method): bool
    {
        return array_key_exists($method, $this->uriHandlers);
    }

    /**
     * Returns the handler for given method if it exists.
     *
     * @param string $method
     * @return null|RequestHandlerInterface
     */
    public function getUriHandlerFor(string $method): ?RequestHandlerInterface
    {
        if (!$this->isMethodAllowed($method)) {
            return null;
        }

        return $this->uriHandlers[$method];
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
