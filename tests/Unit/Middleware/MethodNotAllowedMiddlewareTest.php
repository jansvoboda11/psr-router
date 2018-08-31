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

    public function test_method_failure_results_in_method_not_allowed_response()
    {
        $request = self::createRequest("POST", "/users");
        $request = self::requestWithFailure($request, [
            "POST" => new Handler(),
            "PATCH" => new Handler(),
        ]);

        $this->responseFactory->createResponse(405, "Method Not Allowed")->willReturn(
            self::createResponse(405, "Method Not Allowed")
        );

        $response = $this->middleware->process($request, $this->nextHandler->reveal());

        $this->nextHandler->handle(Argument::any())->shouldNotHaveBeenCalled();

        self::assertEquals(405, $response->getStatusCode());
        self::assertEquals("Method Not Allowed", $response->getReasonPhrase());
        self::assertEquals("POST, PATCH", $response->getHeaderLine("Allow"));
    }

    public function test_uri_failure_is_not_affected()
    {
        $request = self::createRequest("POST", "/users");
        $request = self::requestWithFailure($request, []);

        $notFoundResponse = self::createResponse(404, "Not Found");

        $this->nextHandler->handle($request)->willReturn($notFoundResponse);

        $response = $this->middleware->process($request, $this->nextHandler->reveal());

        self::assertEquals($notFoundResponse, $response);
    }

    public function test_method_that_is_always_allowed_is_not_affected()
    {
        $request = self::createRequest("GET", "/users");

        $getResponse = self::createResponse(200, "OK");

        $this->nextHandler->handle($request)->willReturn($getResponse);

        $response = $this->middleware->process($request, $this->nextHandler->reveal());

        self::assertEquals($getResponse, $response);
    }

    public function test_matched_request_is_not_affected()
    {
        $route = new Route("POST", new StaticPath("/users"), new Handler());

        $request = self::createRequest("POST", "/users");
        $request = self::requestWithMatch($request, $route);

        $postResponse = self::createResponse(201, "Created");

        $this->nextHandler->handle($request)->willReturn($postResponse);

        $response = $this->middleware->process($request, $this->nextHandler->reveal());

        self::assertEquals($postResponse, $response);
    }
}
