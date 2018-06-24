<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Routing match.
 */
class Match
{
    /**
     * @var string
     */
    private $handlerName;

    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * @param string $handlerName
     * @param ServerRequestInterface $request
     */
    public function __construct(string $handlerName, ServerRequestInterface $request)
    {
        $this->handlerName = $handlerName;
        $this->request = $request;
    }

    /**
     * Returns name of the request handler.
     *
     * @return string
     */
    public function getHandlerName(): string
    {
        return $this->handlerName;
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
