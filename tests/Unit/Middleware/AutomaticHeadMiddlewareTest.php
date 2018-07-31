<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit\Middleware;

use Hamcrest\Matchers;
use Mockery;
use Mockery\MockInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Svoboda\Router\Failure;
use Svoboda\Router\Match;
use Svoboda\Router\Middleware\AutomaticHeadMiddleware;
use Svoboda\Router\Router;
use SvobodaTest\Router\Middleware;
use SvobodaTest\Router\TestCase;

class AutomaticHeadMiddlewareTest extends TestCase
{
    /** @var MockInterface|Router */
    private $router;

    /** @var MockInterface|RequestHandlerInterface */
    private $handler;

    /** @var MockInterface|StreamFactoryInterface */
    private $streamFactory;

    /** @var AutomaticHeadMiddleware */
    private $middleware;

    protected function setUp()
    {
        $this->router = Mockery::mock(Router::class);
        $this->streamFactory = Mockery::mock(StreamFactoryInterface::class);
        $this->handler = Mockery::mock(RequestHandlerInterface::class);
        $this->middleware = new AutomaticHeadMiddleware($this->router, $this->streamFactory);
    }

    public function test_it_ignores_non_head_request()
    {
        $request = self::createRequest("POST", "/users");

        $this->router->shouldNotReceive("match");

        $this->handler
            ->shouldReceive("handle")
            ->with($request)
            ->andReturn(self::createResponse(201, "Foobar"))
            ->once();

        $response = $this->middleware->process($request, $this->handler);

        self::assertEquals(201, $response->getStatusCode());
        self::assertEquals("Foobar", $response->getBody());
    }

    public function test_it_ignores_matched_head_route()
    {
        $request = self::createRequest("HEAD", "/users");
        $match = new Match(new Middleware("Users"), $request);
        $request = $request->withAttribute(Match::class, $match);

        $this->router->shouldNotReceive("match");

        $this->handler
            ->shouldReceive("handle")
            ->with($request)
            ->andReturn(self::createResponse()->withHeader("Allow", "GET, POST"))
            ->once();

        $response = $this->middleware->process($request, $this->handler);

        self::assertEquals(["GET, POST"], $response->getHeader("Allow"));
    }

    public function test_it_ignores_uri_failure()
    {
        $request = self::createRequest("HEAD", "/users");
        $failure = new Failure([], $request);
        $request = $request->withAttribute(Failure::class, $failure);

        $this->router->shouldNotReceive("match");

        $this->handler
            ->shouldReceive("handle")
            ->with($request)
            ->andReturn(self::createResponse(404))
            ->once();

        $response = $this->middleware->process($request, $this->handler);

        self::assertEquals(404, $response->getStatusCode());
    }

    public function test_it_ignores_when_get_is_missing()
    {
        $request = self::createRequest("HEAD", "/users");
        $failure = new Failure(["POST"], $request);
        $request = $request->withAttribute(Failure::class, $failure);

        $this->router->shouldNotReceive("match");

        $this->handler
            ->shouldReceive("handle")
            ->with($request)
            ->andReturn(self::createResponse(404))
            ->once();

        $response = $this->middleware->process($request, $this->handler);

        self::assertEquals(404, $response->getStatusCode());
    }

    public function test_it_returns_get_response_with_empty_body()
    {
        $request = self::createRequest("HEAD", "/users");

        $failure = new Failure(["GET"], $request);
        $failureRequest = $request->withAttribute(Failure::class, $failure);

        $getRequest = $request->withMethod("GET");

        $match = new Match(new Middleware("Get"), $request);
        $matchRequest = $getRequest->withAttribute(Match::class, $match);

        $this->router
            ->shouldReceive("match")
            ->with(Matchers::equalTo($getRequest))
            ->andReturn($match)
            ->once();

        $this->handler
            ->shouldReceive("handle")
            ->with(Matchers::equalTo($matchRequest))
            ->andReturn(self::createResponse(201, "The GET body."))
            ->once();

        $this->streamFactory
            ->shouldReceive("createStream")
            ->andReturn(self::createStream())
            ->once();

        $response = $this->middleware->process($failureRequest, $this->handler);

        self::assertEquals(201, $response->getStatusCode());
        self::assertEquals("", $response->getBody());
    }
}
