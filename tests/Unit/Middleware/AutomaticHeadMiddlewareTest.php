<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit\Middleware;

use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Svoboda\Router\Middleware\AutomaticHeadMiddleware;
use Svoboda\Router\Route\Path\StaticPath;
use Svoboda\Router\Route\Route;
use SvobodaTest\Router\FakeHandler;
use SvobodaTest\Router\TestCase;

class AutomaticHeadMiddlewareTest extends TestCase
{
    /** @var ObjectProphecy|RequestHandlerInterface */
    private $nextHandler;

    /** @var ObjectProphecy|StreamFactoryInterface */
    private $streamFactory;

    /** @var AutomaticHeadMiddleware */
    private $middleware;

    protected function setUp()
    {
        $this->streamFactory = $this->prophesize(StreamFactoryInterface::class);
        $this->nextHandler = $this->prophesize(RequestHandlerInterface::class);
        $this->middleware = new AutomaticHeadMiddleware($this->streamFactory->reveal());
    }

    public function test_body_of_get_response_is_erased()
    {
        $getRoute = new Route("GET", new StaticPath("/users"), new FakeHandler());

        $request = self::createRequest("HEAD", "/users");
        $failureRequest = self::requestWithFailure($request, ["GET" => $getRoute]);
        $matchRequest = self::requestWithMatch($request->withMethod("GET"), $getRoute);

        $getResponse = self::createResponse(201, "Created", "The GET body.");

        $this->nextHandler->handle($matchRequest)->willReturn($getResponse);

        $this->streamFactory->createStream()->willReturn(
            self::createStream()
        );

        $response = $this->middleware->process($failureRequest, $this->nextHandler->reveal());

        self::assertEquals($getResponse->getStatusCode(), $response->getStatusCode());
        self::assertEquals($getResponse->getReasonPhrase(), $response->getReasonPhrase());
        self::assertEmpty((string)$response->getBody());
    }

    public function test_explicit_head_route_is_used()
    {
        $headRoute = new Route("HEAD", new StaticPath("/users"), new FakeHandler());

        $request = self::createRequest("HEAD", "/users");
        $request = self::requestWithMatch($request, $headRoute);

        $explicitHeadResponse = self::createResponse(200, "OK");

        $this->nextHandler->handle($request)->willReturn($explicitHeadResponse);

        $response = $this->middleware->process($request, $this->nextHandler->reveal());

        self::assertEquals($explicitHeadResponse, $response);
    }

    public function test_non_head_request_is_not_affected()
    {
        $request = self::createRequest("POST", "/users");

        $postResponse = self::createResponse(201, "Created", "New user was created.");

        $this->nextHandler->handle($request)->willReturn($postResponse);

        $response = $this->middleware->process($request, $this->nextHandler->reveal());

        self::assertEquals($postResponse, $response);
    }

    public function test_head_request_with_uri_failure_is_not_affected()
    {
        $request = self::createRequest("HEAD", "/users");
        $request = self::requestWithFailure($request, []);

        $notFoundResponse = self::createResponse(404, "Not Found", "This page doesn't exist.");

        $this->nextHandler->handle($request)->willReturn($notFoundResponse);

        $response = $this->middleware->process($request, $this->nextHandler->reveal());

        self::assertEquals($notFoundResponse, $response);
    }

    public function test_head_request_with_method_failure_is_not_affected()
    {
        $request = self::createRequest("HEAD", "/users");
        $request = self::requestWithFailure($request, ["POST" => new FakeHandler()]);

        $notFoundResponse = self::createResponse(404, "Not Found", "This page doesn't exist.");

        $this->nextHandler->handle($request)->willReturn($notFoundResponse);

        $response = $this->middleware->process($request, $this->nextHandler->reveal());

        self::assertEquals($notFoundResponse, $response);
    }
}
