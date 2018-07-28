<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Middleware;

use Mockery;
use Mockery\MockInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Svoboda\Router\Match;
use Svoboda\Router\Middleware\RouteDispatchingMiddleware;
use SvobodaTest\Router\TestCase;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

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
        $request = new ServerRequest();
        $emptyResponse = new Response();

        $this->handler
            ->shouldReceive("handle")
            ->with($request)
            ->andReturn($emptyResponse)
            ->once();

        $response = $this->middleware->process($request, $this->handler);

        self::assertEquals($emptyResponse, $response);
    }

    public function test_it_delegates_to_match_middleware_when_present()
    {
        $matchResponse = new Response();

        $request = new ServerRequest();

        $this->handler->shouldNotReceive("handle");

        $middleware = Mockery::mock(MiddlewareInterface::class);
        $middleware
            ->shouldReceive("process")
            ->with($request, $this->handler)
            ->andReturn($matchResponse)
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

        self::assertEquals($matchResponse, $response);
    }
}
