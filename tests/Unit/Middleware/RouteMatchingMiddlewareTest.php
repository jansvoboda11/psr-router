<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit\Middleware;

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
        $request = self::createRequest("GET", "/users");
        $match = new Match(new Middleware("Match"), $request);
        $requestWithMatch = $request->withAttribute(Match::class, $match);

        $this->router
            ->shouldReceive("match")
            ->with($request)
            ->andReturn($match)
            ->once();

        $this->handler
            ->shouldReceive("handle")
            ->with(Matchers::equalTo($requestWithMatch))
            ->andReturn(self::createResponse(404))
            ->once();

        $response = $this->middleware->process($request, $this->handler);

        self::assertSame(404, $response->getStatusCode());
    }

    public function test_it_adds_failure_attribute()
    {
        $request = self::createRequest("PATCH", "/users");
        $failure = new Failure(["GET", "POST"], $request);
        $requestWithFailure = $request->withAttribute(Failure::class, $failure);

        $this->router
            ->shouldReceive("match")
            ->with($request)
            ->andThrow($failure)
            ->once();

        $this->handler
            ->shouldReceive("handle")
            ->with(Matchers::equalTo($requestWithFailure))
            ->andReturn(self::createResponse(404))
            ->once();

        $response = $this->middleware->process($request, $this->handler);

        self::assertSame(404, $response->getStatusCode());
    }
}
