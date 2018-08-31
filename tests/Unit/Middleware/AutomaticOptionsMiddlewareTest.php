<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit\Middleware;

use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Svoboda\Router\Middleware\AutomaticOptionsMiddleware;
use Svoboda\Router\Route\Path\StaticPath;
use Svoboda\Router\Route\Route;
use SvobodaTest\Router\FakeHandler;
use SvobodaTest\Router\TestCase;

class AutomaticOptionsMiddlewareTest extends TestCase
{
    /** @var ObjectProphecy|RequestHandlerInterface */
    private $nextHandler;

    /** @var ObjectProphecy|ResponseFactoryInterface */
    private $responseFactory;

    /** @var AutomaticOptionsMiddleware */
    private $middleware;

    protected function setUp()
    {
        $this->nextHandler = $this->prophesize(RequestHandlerInterface::class);
        $this->responseFactory = $this->prophesize(ResponseFactoryInterface::class);
        $this->middleware = new AutomaticOptionsMiddleware($this->responseFactory->reveal());
    }

    public function test_allowed_methods_are_reported()
    {
        $request = self::createRequest("OPTIONS", "/users");
        $request = self::requestWithFailure($request, [
            "GET" => new FakeHandler(),
            "POST" => new FakeHandler(),
        ]);

        $this->responseFactory->createResponse(200, "OK")->willReturn(
            self::createResponse(200, "OK")
        );

        $response = $this->middleware->process($request, $this->nextHandler->reveal());

        $this->nextHandler->handle(Argument::any())->shouldNotHaveBeenCalled();

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals("OK", $response->getReasonPhrase());
        self::assertEquals("GET, POST", $response->getHeaderLine("Options"));
    }

    public function test_explicit_options_route_is_used()
    {
        $optionsRoute = new Route("OPTIONS", new StaticPath("/users"), new FakeHandler());

        $request = self::createRequest("OPTIONS", "/users");
        $request = self::requestWithMatch($request, $optionsRoute);

        $optionsResponse = self::createResponse()->withHeader("Allow", "GET, POST");

        $this->nextHandler->handle($request)->willReturn($optionsResponse);

        $response = $this->middleware->process($request, $this->nextHandler->reveal());

        self::assertEquals($optionsResponse, $response);
    }

    public function test_non_options_request_is_not_affected()
    {
        $request = self::createRequest("POST", "/users");

        $postResponse = self::createResponse(201);

        $this->nextHandler->handle($request)->willReturn($postResponse);

        $response = $this->middleware->process($request, $this->nextHandler->reveal());

        self::assertEquals($postResponse, $response);
    }

    public function test_request_with_uri_failure_is_not_affected()
    {
        $request = self::createRequest("OPTIONS", "/users");
        $request = self::requestWithFailure($request, []);

        $notFoundResponse = self::createResponse(404);

        $this->nextHandler->handle($request)->willReturn($notFoundResponse);

        $response = $this->middleware->process($request, $this->nextHandler->reveal());

        self::assertEquals($notFoundResponse, $response);
    }
}
