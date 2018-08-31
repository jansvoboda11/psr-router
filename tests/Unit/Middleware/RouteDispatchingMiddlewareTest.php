<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit\Middleware;

use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Server\RequestHandlerInterface;
use Svoboda\Router\Middleware\RouteDispatchingMiddleware;
use Svoboda\Router\Route\Path\StaticPath;
use Svoboda\Router\Route\Route;
use SvobodaTest\Router\TestCase;

class RouteDispatchingMiddlewareTest extends TestCase
{
    /** @var ObjectProphecy|RequestHandlerInterface */
    private $nextHandler;

    /** @var RouteDispatchingMiddleware */
    private $middleware;

    protected function setUp()
    {
        $this->nextHandler = $this->prophesize(RequestHandlerInterface::class);
        $this->middleware = new RouteDispatchingMiddleware();
    }

    public function test_match_handler_is_used_on_success()
    {
        $request = self::createRequest("POST", "/users");

        $postResponse = self::createResponse(201, "Created");

        /** @var ObjectProphecy|RequestHandlerInterface $postHandler */
        $postHandler = $this->prophesize(RequestHandlerInterface::class);
        $postHandler->handle($request)->willReturn($postResponse);

        $route = new Route("POST", new StaticPath("/users"), $postHandler->reveal());

        $request = self::requestWithMatch($request, $route);

        $response = $this->middleware->process($request, $this->nextHandler->reveal());

        $this->nextHandler->handle(Argument::any())->shouldNotHaveBeenCalled();

        self::assertEquals($postResponse, $response);
    }

    public function test_default_handler_is_used_on_failure()
    {
        $request = self::createRequest("GET", "/users");

        $defaultResponse = self::createResponse(201);

        $this->nextHandler->handle($request)->willReturn($defaultResponse);

        $response = $this->middleware->process($request, $this->nextHandler->reveal());

        self::assertEquals($defaultResponse, $response);
    }
}
