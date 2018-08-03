<?php

declare(strict_types=1);

namespace SvobodaTest\Router;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Middleware implements MiddlewareInterface
{
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        throw new Exception("Not implemented");
    }
}
