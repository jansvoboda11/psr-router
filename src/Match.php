<?php

declare(strict_types=1);

namespace Svoboda\Router;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;

/**
 * Routing match.
 */
class Match
{
    /**
     * The registered middleware.
     *
     * @var MiddlewareInterface
     */
    private $middleware;

    /**
     * The matched request with all route attributes.
     *
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * Constructor.
     *
     * @param MiddlewareInterface $middleware
     * @param ServerRequestInterface $request
     */
    public function __construct(MiddlewareInterface $middleware, ServerRequestInterface $request)
    {
        $this->middleware = $middleware;
        $this->request = $request;
    }

    /**
     * Returns the middleware.
     *
     * @return MiddlewareInterface
     */
    public function getMiddleware(): MiddlewareInterface
    {
        return $this->middleware;
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
