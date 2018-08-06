<?php

declare(strict_types=1);

namespace Svoboda\Router;

use Psr\Http\Message\ServerRequestInterface;
use Svoboda\Router\Route\Route;

/**
 * Routing match.
 */
class Match
{
    /**
     * The matched route.
     *
     * @var Route
     */
    private $route;

    /**
     * The matched request with all route attributes.
     *
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * Constructor.
     *
     * @param Route $route
     * @param ServerRequestInterface $request
     */
    public function __construct(Route $route, ServerRequestInterface $request)
    {
        $this->route = $route;
        $this->request = $request;
    }

    /**
     * Returns the route.
     *
     * @return Route
     */
    public function getRoute(): Route
    {
        return $this->route;
    }

    /**
     * Returns request containing all route attributes.
     *
     * @return ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }
}
