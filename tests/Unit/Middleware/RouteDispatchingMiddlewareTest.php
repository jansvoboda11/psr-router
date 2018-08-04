<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit\Middleware;

use Mockery;
use Mockery\MockInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Svoboda\Router\Match;
use Svoboda\Router\Middleware\RouteDispatchingMiddleware;
use SvobodaTest\Router\TestCase;

class RouteDispatchingMiddlewareTest extends TestCase
{
    /** @var MockInterface|RequestHandlerInterface */
    private $handler;

    /** @var RouteDispatchingMiddleware */
    private $middleware;

    protected function setUp()
    {
        $this->handler = Mockery::mock(RequestHandlerInterface::class);
        $this->middleware = new RouteDispatchingMiddleware();
    }

    public function test_it_calls_default_handler_without_match()
    {
        $request = self::createRequest("GET", "/users");

        $this->handler
            ->shouldReceive("handle")
            ->with($request)
            ->andReturn(self::createResponse(201, "Created", "Default Handler Response"))
            ->once();

        $response = $this->middleware->process($request, $this->handler);

        self::assertEquals(201, $response->getStatusCode());
        self::assertEquals("Default Handler Response", $response->getBody());
    }

    public function test_it_delegates_to_match_middleware_when_present()
    {
        $request = self::createRequest("GET", "/users");

        $this->handler->shouldNotReceive("handle");

        $middleware = Mockery::mock(MiddlewareInterface::class);
        $middleware
            ->shouldReceive("process")
            ->with($request, $this->handler)
            ->andReturn(self::createResponse(201))
            ->once();

        $match = Mockery::mock(Match::class);
        $match
            ->shouldReceive("getMiddleware")
            ->andReturn($middleware)
            ->once();
        $match
            ->shouldReceive("getRequest")
            ->andReturn($request)
            ->once();

        $request = $request->withAttribute(Match::class, $match);

        $response = $this->middleware->process($request, $this->handler);

        self::assertEquals(201, $response->getStatusCode());
    }
}
