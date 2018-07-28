<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Middleware;

use Hamcrest\Matchers;
use Mockery;
use Mockery\MockInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Svoboda\Router\Failure;
use Svoboda\Router\Match;
use Svoboda\Router\Middleware\RouteMatchingMiddleware;
use Svoboda\Router\Router;
use SvobodaTest\Router\Middleware;
use SvobodaTest\Router\TestCase;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

class RouteMatchingMiddlewareTest extends TestCase
{
    /** @var MockInterface|Router */
    private $router;

    /** @var MockInterface|RequestHandlerInterface */
    private $handler;

    /** @var RouteMatchingMiddleware */
    private $middleware;

    protected function setUp()
    {
        $this->router = Mockery::mock(Router::class);
        $this->handler = Mockery::mock(RequestHandlerInterface::class);
        $this->middleware = new RouteMatchingMiddleware($this->router);
    }

    public function test_it_adds_match_attribute()
    {
        $request = new ServerRequest();
        $match = new Match(new Middleware("Match"), $request);
        $requestWithMatch = $request->withAttribute(Match::class, $match);
        $emptyResponse = new Response();

        $this->router
            ->shouldReceive("match")
            ->with($request)
            ->andReturn($match)
            ->once();

        $this->handler
            ->shouldReceive("handle")
            ->with(Matchers::equalTo($requestWithMatch))
            ->andReturn($emptyResponse)
            ->once();

        $response = $this->middleware->process($request, $this->handler);

        self::assertSame($emptyResponse, $response);
    }

    public function test_it_adds_failure_attribute()
    {
        $request = new ServerRequest();
        $failure = new Failure(["GET", "POST"], $request);
        $requestWithFailure = $request->withAttribute(Failure::class, $failure);
        $emptyResponse = new Response();
        
        $this->router
            ->shouldReceive("match")
            ->with($request)
            ->andThrow($failure)
            ->once();

        $this->handler
            ->shouldReceive("handle")
            ->with(Matchers::equalTo($requestWithFailure))
            ->andReturn($emptyResponse)
            ->once();

        $response = $this->middleware->process($request, $this->handler);

        self::assertSame($emptyResponse, $response);
    }
}
