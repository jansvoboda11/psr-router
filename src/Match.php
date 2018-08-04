<?php

declare(strict_types=1);

namespace Svoboda\Router;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Routing match.
 */
class Match
{
    /**
     * The registered handler.
     *
     * @var RequestHandlerInterface
     */
    private $handler;

    /**
     * The matched request with all route attributes.
     *
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * Constructor.
     *
     * @param RequestHandlerInterface $handler
     * @param ServerRequestInterface $request
     */
    public function __construct(RequestHandlerInterface $handler, ServerRequestInterface $request)
    {
        $this->handler = $handler;
        $this->request = $request;
    }

    /**
     * Returns the handler.
     *
     * @return RequestHandlerInterface
     */
    public function getHandler(): RequestHandlerInterface
    {
        return $this->handler;
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
