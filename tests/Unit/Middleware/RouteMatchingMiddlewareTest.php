<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit\Middleware;

use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Server\RequestHandlerInterface;
use Svoboda\Router\Failure;
use Svoboda\Router\Match;
use Svoboda\Router\Middleware\RouteMatchingMiddleware;
use Svoboda\Router\Route\Path\StaticPath;
use Svoboda\Router\Route\Route;
use Svoboda\Router\Router;
use SvobodaTest\Router\FakeHandler;
use SvobodaTest\Router\TestCase;

class RouteMatchingMiddlewareTest extends TestCase
{
    /** @var ObjectProphecy|Router */
    private $router;

    /** @var ObjectProphecy|RequestHandlerInterface */
    private $nextHandler;

    /** @var RouteMatchingMiddleware */
    private $middleware;

    protected function setUp()
    {
        $this->router = $this->prophesize(Router::class);
        $this->nextHandler = $this->prophesize(RequestHandlerInterface::class);
        $this->middleware = new RouteMatchingMiddleware($this->router->reveal());
    }

    public function test_match_is_added_on_success()
    {
        $route = new Route("POST", new StaticPath("/users"), new FakeHandler());

        $request = self::createRequest("GET", "/users");
        $match = new Match($route, $request);
        $requestWithMatch = $request->withAttribute(Match::class, $match);

        $matchResponse = self::createResponse(201, "Created");

        $this->router->match($request)->willReturn($match);

        $this->nextHandler->handle($requestWithMatch)->willReturn($matchResponse);

        $response = $this->middleware->process($request, $this->nextHandler->reveal());

        self::assertEquals($matchResponse, $response);
    }

    public function test_failure_is_added_on_fail()
    {
        $request = self::createRequest("PATCH", "/users");
        $failure = new Failure(["GET", "POST"], $request);
        $requestWithFailure = $request->withAttribute(Failure::class, $failure);

        $notFoundResponse = self::createResponse(404);

        $this->router->match($request)->willThrow($failure);

        $this->nextHandler->handle($requestWithFailure)->willReturn($notFoundResponse);

        $response = $this->middleware->process($request, $this->nextHandler->reveal());

        self::assertEquals($notFoundResponse, $response);
    }
}
