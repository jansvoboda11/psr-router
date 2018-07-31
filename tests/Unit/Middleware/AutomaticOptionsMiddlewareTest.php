<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit\Middleware;

use Mockery;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Svoboda\Router\Failure;
use Svoboda\Router\Match;
use Svoboda\Router\Middleware\AutomaticOptionsMiddleware;
use SvobodaTest\Router\Middleware;
use SvobodaTest\Router\TestCase;

class AutomaticOptionsMiddlewareTest extends TestCase
{
    /** @var MockInterface|RequestHandlerInterface */
    private $handler;

    /** @var MockInterface|ResponseFactoryInterface */
    private $responseFactory;

    /** @var AutomaticOptionsMiddleware */
    private $middleware;

    protected function setUp()
    {
        $this->handler = Mockery::mock(RequestHandlerInterface::class);
        $this->responseFactory = Mockery::mock(ResponseFactoryInterface::class);
        $this->middleware = new AutomaticOptionsMiddleware($this->responseFactory);
    }

    public function test_it_ignores_non_options_request()
    {
        $request = self::createRequest("GET", "/users");

        $this->handler
            ->shouldReceive("handle")
            ->with($request)
            ->andReturn(self::createResponse(201, "Foobar"))
            ->once();

        $response = $this->middleware->process($request, $this->handler);

        self::assertEquals(201, $response->getStatusCode());
        self::assertEquals("Foobar", $response->getBody());
    }

    public function test_it_ignores_matched_route()
    {
        $request = self::createRequest("OPTIONS", "/users");
        $match = new Match(new Middleware("Users"), $request);
        $request = $request->withAttribute(Match::class, $match);

        $this->handler
            ->shouldReceive("handle")
            ->with($request)
            ->andReturn(self::createResponse(201, "Foobar"))
            ->once();

        $response = $this->middleware->process($request, $this->handler);

        self::assertEquals(201, $response->getStatusCode());
        self::assertEquals("Foobar", $response->getBody());
    }

    public function test_it_ignores_uri_failure()
    {
        $request = self::createRequest("OPTIONS", "/users");
        $failure = new Failure([], $request);
        $request = $request->withAttribute(Failure::class, $failure);

        $this->handler
            ->shouldReceive("handle")
            ->with($request)
            ->andReturn(self::createResponse(201, "Foobar"))
            ->once();

        $response = $this->middleware->process($request, $this->handler);

        self::assertEquals(201, $response->getStatusCode());
        self::assertEquals("Foobar", $response->getBody());
    }

    public function test_it_returns_allowed_methods()
    {
        $request = self::createRequest("OPTIONS", "/users");
        $failure = new Failure(["GET", "POST"], $request);
        $request = $request->withAttribute(Failure::class, $failure);

        $this->responseFactory
            ->shouldReceive("createResponse")
            ->andReturn(self::createResponse());

        $this->handler->shouldNotReceive("handle");

        $response = $this->middleware->process($request, $this->handler);

        self::assertEquals(["GET, POST"], $response->getHeader("Options"));
    }
}
