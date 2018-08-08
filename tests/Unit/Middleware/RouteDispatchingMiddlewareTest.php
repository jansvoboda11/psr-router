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

    public function test_it_calls_next_handler_without_match()
    {
        $request = self::createRequest("GET", "/users");

        $nextHandlerResponse = self::createResponse(201);

        $this->nextHandler->handle($request)->willReturn($nextHandlerResponse);

        $response = $this->middleware->process($request, $this->nextHandler->reveal());

        self::assertEquals($nextHandlerResponse, $response);
    }

    public function test_it_delegates_to_match_middleware_when_present()
    {
        $request = self::createRequest("GET", "/users");

        $matchedHandlerResponse = self::createResponse(201);

        /** @var ObjectProphecy|RequestHandlerInterface $matchedHandler */
        $matchedHandler = $this->prophesize(RequestHandlerInterface::class);
        $matchedHandler->handle($request)->willReturn($matchedHandlerResponse);

        $route = new Route("GET", new StaticPath("/users"), $matchedHandler->reveal());

        $request = self::requestWithMatch($request, $route);

        $response = $this->middleware->process($request, $this->nextHandler->reveal());

        $this->nextHandler->handle(Argument::any())->shouldNotHaveBeenCalled();

        self::assertEquals($matchedHandlerResponse, $response);
    }
}
