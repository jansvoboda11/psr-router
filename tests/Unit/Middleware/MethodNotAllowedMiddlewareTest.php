<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit\Middleware;

use Mockery;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Svoboda\Router\Failure;
use Svoboda\Router\Match;
use Svoboda\Router\Middleware\MethodNotAllowedMiddleware;
use Svoboda\Router\Route\Path\StaticPath;
use Svoboda\Router\Route\Route;
use SvobodaTest\Router\Handler;
use SvobodaTest\Router\TestCase;

class MethodNotAllowedMiddlewareTest extends TestCase
{
    /** @var MockInterface|RequestHandlerInterface */
    private $handler;

    /** @var MockInterface|ResponseFactoryInterface */
    private $responseFactory;

    /** @var MethodNotAllowedMiddleware */
    private $middleware;

    protected function setUp()
    {
        $this->handler = Mockery::mock(RequestHandlerInterface::class);
        $this->responseFactory = Mockery::mock(ResponseFactoryInterface::class);
        $this->middleware = new MethodNotAllowedMiddleware($this->responseFactory);
    }

    public function test_it_ignores_request_with_method_that_is_always_allowed()
    {
        $request = self::createRequest("GET", "/users");

        $this->handler
            ->shouldReceive("handle")
            ->with($request)
            ->andReturn(self::createResponse(201, "Created", "Foobar"))
            ->once();

        $response = $this->middleware->process($request, $this->handler);

        self::assertEquals(201, $response->getStatusCode());
        self::assertEquals("Foobar", $response->getBody());
    }

    public function test_it_ignores_matched_route()
    {
        $request = self::createRequest("POST", "/users");
        $route = new Route("POST", new StaticPath("/users"), new Handler("Users"));
        $match = new Match($route, $request);
        $request = $request->withAttribute(Match::class, $match);

        $this->handler
            ->shouldReceive("handle")
            ->with($request)
            ->andReturn(self::createResponse(201, "Created", "Foobar"))
            ->once();

        $response = $this->middleware->process($request, $this->handler);

        self::assertEquals(201, $response->getStatusCode());
        self::assertEquals("Foobar", $response->getBody());
    }

    public function test_it_ignores_uri_failure()
    {
        $request = self::createRequest("POST", "/users");
        $failure = new Failure([], $request);
        $request = $request->withAttribute(Failure::class, $failure);

        $this->handler
            ->shouldReceive("handle")
            ->with($request)
            ->andReturn(self::createResponse(201, "Created", "Foobar"))
            ->once();

        $response = $this->middleware->process($request, $this->handler);

        self::assertEquals(201, $response->getStatusCode());
        self::assertEquals("Foobar", $response->getBody());
    }

    public function test_it_returns_method_not_allowed_response()
    {
        $request = self::createRequest("POST", "/users");
        $failure = new Failure([
            "POST" => new Handler("Post"),
            "PATCH" => new Handler("Patch"),
        ], $request);
        $request = $request->withAttribute(Failure::class, $failure);

        $this->responseFactory
            ->shouldReceive("createResponse")
            ->with(405, "Method Not Allowed")
            ->andReturn(self::createResponse(405, "Method Not Allowed"))
            ->once();

        $this->handler->shouldNotReceive("handle");

        $response = $this->middleware->process($request, $this->handler);

        self::assertEquals(405, $response->getStatusCode());
        self::assertEquals("Method Not Allowed", $response->getReasonPhrase());
        self::assertEquals(["POST, PATCH"], $response->getHeader("Allow"));
    }
}
