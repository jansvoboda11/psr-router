<?php

declare(strict_types=1);

namespace Svoboda\Router;

use Psr\Http\Message\ServerRequestInterface;
use Svoboda\Router\Route\Route;

/**
 * Exception representing routing failure - no exact route match.
 */
class Failure extends Exception
{
    /**
     * Routes that would match the URI in a combination with a different HTTP method.
     * Array keys are their respective HTTP methods.
     *
     * @var Route[]
     */
    private $uriRoutes;

    /**
     * The original request.
     *
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * Constructor.
     *
     * @param Route[] $uriRoutes
     * @param ServerRequestInterface $request
     */
    public function __construct(array $uriRoutes, ServerRequestInterface $request)
    {
        $acceptableMethods = implode(", ", array_keys($uriRoutes));

        parent::__construct("Failed to match incoming request, acceptable methods: [$acceptableMethods]");

        $this->uriRoutes = $uriRoutes;
        $this->request = $request;
    }

    /**
     * Returns routes that would match the URI in a combination with a different HTTP method.
     * Array keys are their respective HTTP methods.
     *
     * @return Route[]
     */
    public function getUriRoutes(): array
    {
        return $this->uriRoutes;
    }

    /**
     * Determines whether the failure was caused by an incorrect HTTP method.
     *
     * @return bool
     */
    public function isMethodFailure(): bool
    {
        return !empty($this->uriRoutes);
    }

    /**
     * Returns the allowed methods.
     *
     * @return string[]
     */
    public function getAllowedMethods(): array
    {
        return array_keys($this->uriRoutes);
    }

    /**
     * Determine if the given method is allowed.
     *
     * @param string $method
     * @return bool
     */
    public function isMethodAllowed(string $method): bool
    {
        return array_key_exists($method, $this->uriRoutes);
    }

    /**
     * Returns the route for the given method if it exists.
     *
     * @param string $method
     * @return null|Route
     */
    public function getUriRouteFor(string $method): ?Route
    {
        if (!$this->isMethodAllowed($method)) {
            return null;
        }

        return $this->uriRoutes[$method];
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
