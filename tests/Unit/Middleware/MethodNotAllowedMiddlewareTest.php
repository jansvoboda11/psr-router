<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit\Middleware;

use Mockery;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Svoboda\Router\Failure;
use Svoboda\Router\Match;
use Svoboda\Router\Middleware\MethodNotAllowedMiddleware;
use SvobodaTest\Router\Middleware;
use SvobodaTest\Router\TestCase;
use Zend\Diactoros\Response\JsonResponse;

class MethodNotAllowedMiddlewareTest extends TestCase
{
    /** @var MockInterface|RequestHandlerInterface */
    private $handler;

    /** @var ResponseInterface */
    private $emptyResponse;

    /** @var MethodNotAllowedMiddleware */
    private $middleware;

    protected function setUp()
    {
        $this->handler = Mockery::mock(RequestHandlerInterface::class);
        $this->emptyResponse = self::createResponse();
        $this->middleware = new MethodNotAllowedMiddleware($this->emptyResponse);
    }

    public function test_it_ignores_request_with_method_that_is_always_allowed()
    {
        $request = self::createRequest("GET", "/users");

        $handlerResponse = new JsonResponse([["foo" => "bar"]]);

        $this->handler
            ->shouldReceive("handle")
            ->with($request)
            ->andReturn($handlerResponse)
            ->once();

        $response = $this->middleware->process($request, $this->handler);

        self::assertEquals($handlerResponse, $response);
    }

    public function test_it_ignores_matched_route()
    {
        $request = self::createRequest("POST", "/users");
        $match = new Match(new Middleware("Users"), $request);
        $request = $request->withAttribute(Match::class, $match);

        $handlerResponse = new JsonResponse(["foo" => "bar"]);

        $this->handler
            ->shouldReceive("handle")
            ->with($request)
            ->andReturn($handlerResponse)
            ->once();

        $response = $this->middleware->process($request, $this->handler);

        self::assertEquals($handlerResponse, $response);
    }

    public function test_it_ignores_uri_failure()
    {
        $request = self::createRequest("POST", "/users");
        $failure = new Failure([], $request);
        $request = $request->withAttribute(Failure::class, $failure);

        $handlerResponse = new JsonResponse(["foo" => "bar"]);

        $this->handler
            ->shouldReceive("handle")
            ->with($request)
            ->andReturn($handlerResponse)
            ->once();

        $response = $this->middleware->process($request, $this->handler);

        self::assertEquals($handlerResponse, $response);
    }

    public function test_it_returns_method_not_allowed_response()
    {
        $request = self::createRequest("POST", "/users");
        $failure = new Failure(["POST", "PATCH"], $request);
        $request = $request->withAttribute(Failure::class, $failure);

        $allowResponse = $this->emptyResponse
            ->withStatus(405, "Method Not Allowed")
            ->withHeader("Allow", "POST, PATCH");

        $this->handler->shouldNotReceive("handle");

        $response = $this->middleware->process($request, $this->handler);

        self::assertEquals($allowResponse, $response);
    }
}
