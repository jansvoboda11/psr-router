<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit\Middleware;

use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Svoboda\Router\Middleware\MethodNotAllowedMiddleware;
use Svoboda\Router\Route\Path\StaticPath;
use Svoboda\Router\Route\Route;
use SvobodaTest\Router\Handler;
use SvobodaTest\Router\TestCase;

class MethodNotAllowedMiddlewareTest extends TestCase
{
    /** @var ObjectProphecy|RequestHandlerInterface */
    private $nextHandler;

    /** @var ObjectProphecy|ResponseFactoryInterface */
    private $responseFactory;

    /** @var MethodNotAllowedMiddleware */
    private $middleware;

    protected function setUp()
    {
        $this->nextHandler = $this->prophesize(RequestHandlerInterface::class);
        $this->responseFactory = $this->prophesize(ResponseFactoryInterface::class);
        $this->middleware = new MethodNotAllowedMiddleware($this->responseFactory->reveal());
    }

    public function test_it_ignores_request_with_method_that_is_always_allowed()
    {
        $request = self::createRequest("GET", "/users");

        $nextHandlerResponse = self::createResponse(201, "Created", "Foobar");

        $this->nextHandler->handle($request)->willReturn($nextHandlerResponse);

        $response = $this->middleware->process($request, $this->nextHandler->reveal());

        self::assertEquals($nextHandlerResponse, $response);
    }

    public function test_it_ignores_matched_route()
    {
        $route = new Route("POST", new StaticPath("/users"), new Handler("Users"));

        $request = self::createRequest("POST", "/users");
        $request = self::requestWithMatch($request, $route);

        $nextHandlerResponse = self::createResponse(201, "Created", "Foobar");

        $this->nextHandler->handle($request)->willReturn($nextHandlerResponse);

        $response = $this->middleware->process($request, $this->nextHandler->reveal());

        self::assertEquals($nextHandlerResponse, $response);
    }

    public function test_it_ignores_uri_failure()
    {
        $request = self::createRequest("POST", "/users");
        $request = self::requestWithFailure($request, []);

        $nextHandlerResponse = self::createResponse(201, "Created", "Foobar");

        $this->nextHandler->handle($request)->willReturn($nextHandlerResponse);

        $response = $this->middleware->process($request, $this->nextHandler->reveal());

        self::assertEquals(201, $response->getStatusCode());
        self::assertEquals("Foobar", $response->getBody());
    }

    public function test_it_returns_method_not_allowed_response()
    {
        $request = self::createRequest("POST", "/users");
        $request = self::requestWithFailure($request, [
            "POST" => new Handler("Post"),
            "PATCH" => new Handler("Patch"),
        ]);

        $factoryResponse = self::createResponse(405, "Method Not Allowed");

        $this->responseFactory->createResponse(405, "Method Not Allowed")->willReturn($factoryResponse);

        $response = $this->middleware->process($request, $this->nextHandler->reveal());

        $this->nextHandler->handle(Argument::any())->shouldNotHaveBeenCalled();

        self::assertEquals(405, $response->getStatusCode());
        self::assertEquals("Method Not Allowed", $response->getReasonPhrase());
        self::assertEquals(["POST, PATCH"], $response->getHeader("Allow"));
    }
}
