<?php

declare(strict_types=1);

namespace SvobodaTest\Router;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Handler implements RequestHandlerInterface
{
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        throw new Exception("Not implemented");
    }
}
