<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit\Middleware;

use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Svoboda\Router\Middleware\AutomaticHeadMiddleware;
use Svoboda\Router\Route\Path\StaticPath;
use Svoboda\Router\Route\Route;
use SvobodaTest\Router\Handler;
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

    public function test_it_ignores_non_head_request()
    {
        $request = self::createRequest("POST", "/users");

        $nextHandlerResponse = self::createResponse(201, "Created", "Foobar");

        $this->nextHandler->handle($request)->willReturn($nextHandlerResponse);

        $response = $this->middleware->process($request, $this->nextHandler->reveal());

        self::assertEquals($nextHandlerResponse, $response);
    }

    public function test_it_ignores_matched_head_route()
    {
        $route = new Route("HEAD", new StaticPath("/users"), new Handler("Users"));

        $request = self::createRequest("HEAD", "/users");
        $request = self::requestWithMatch($request, $route);

        $nextHandlerResponse = self::createResponse()->withHeader("Allow", "GET, POST");

        $this->nextHandler->handle($request)->willReturn($nextHandlerResponse);

        $response = $this->middleware->process($request, $this->nextHandler->reveal());

        self::assertEquals(["GET, POST"], $response->getHeader("Allow"));
    }

    public function test_it_ignores_uri_failure()
    {
        $request = self::createRequest("HEAD", "/users");
        $request = self::requestWithFailure($request, []);

        $nextHandlerResponse = self::createResponse(404);

        $this->nextHandler->handle($request)->willReturn($nextHandlerResponse);

        $response = $this->middleware->process($request, $this->nextHandler->reveal());

        self::assertEquals($nextHandlerResponse, $response);
    }

    public function test_it_ignores_when_get_is_missing()
    {
        $request = self::createRequest("HEAD", "/users");
        $request = self::requestWithFailure($request, ["POST" => new Handler("Post")]);

        $nextHandlerResponse = self::createResponse(404);

        $this->nextHandler->handle($request)->willReturn($nextHandlerResponse);

        $response = $this->middleware->process($request, $this->nextHandler->reveal());

        self::assertEquals($nextHandlerResponse, $response);
    }

    public function test_it_returns_get_response_with_empty_body()
    {
        $getRoute = new Route("GET", new StaticPath("/users"), new Handler("Users"));

        $request = self::createRequest("HEAD", "/users");
        $failureRequest = self::requestWithFailure($request, ["GET" => $getRoute]);
        $matchRequest = self::requestWithMatch($request->withMethod("GET"), $getRoute);

        $nextHandlerResponse = self::createResponse(201, "Created", "The GET body.");
        $factoryStream = self::createStream();

        $this->nextHandler->handle($matchRequest)->willReturn($nextHandlerResponse);

        $this->streamFactory->createStream()->willReturn($factoryStream);

        $response = $this->middleware->process($failureRequest, $this->nextHandler->reveal());

        self::assertEquals($nextHandlerResponse->getStatusCode(), $response->getStatusCode());
        self::assertEquals($nextHandlerResponse->getReasonPhrase(), $response->getReasonPhrase());
        self::assertEmpty((string)$response->getBody());
    }
}
