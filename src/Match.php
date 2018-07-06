<?php

declare(strict_types=1);

namespace Svoboda\Router;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Routing match.
 */
class Match
{
    /**
     * The registered handler.
     *
     * @var mixed
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
     * @param mixed $handler
     * @param ServerRequestInterface $request
     */
    public function __construct($handler, ServerRequestInterface $request)
    {
        $this->handler = $handler;
        $this->request = $request;
    }

    /**
     * Returns name of the request handler.
     *
     * @return mixed
     */
    public function getHandler()
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
