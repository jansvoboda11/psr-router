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
use SvobodaTest\Router\Handler;
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

    public function test_it_ignores_non_options_request()
    {
        $request = self::createRequest("GET", "/users");

        $nextHandlerResponse = self::createResponse(201, "Created", "Foobar");

        $this->nextHandler->handle($request)->willReturn($nextHandlerResponse);

        $response = $this->middleware->process($request, $this->nextHandler->reveal());

        self::assertEquals(201, $response->getStatusCode());
        self::assertEquals("Foobar", $response->getBody());
    }

    public function test_it_ignores_matched_route()
    {
        $route = new Route("OPTIONS", new StaticPath("/users"), new Handler("Users"));

        $request = self::createRequest("OPTIONS", "/users");
        $request = self::requestWithMatch($request, $route);

        $nextHandlerResponse = self::createResponse(201, "Created", "Foobar");

        $this->nextHandler->handle($request)->willReturn($nextHandlerResponse);

        $response = $this->middleware->process($request, $this->nextHandler->reveal());

        self::assertEquals(201, $response->getStatusCode());
        self::assertEquals("Foobar", $response->getBody());
    }

    public function test_it_ignores_uri_failure()
    {
        $request = self::createRequest("OPTIONS", "/users");
        $request = self::requestWithFailure($request, []);

        $nextHandlerResponse = self::createResponse(201, "Created", "Foobar");

        $this->nextHandler->handle($request)->willReturn($nextHandlerResponse);

        $response = $this->middleware->process($request, $this->nextHandler->reveal());

        self::assertEquals(201, $response->getStatusCode());
        self::assertEquals("Foobar", $response->getBody());
    }

    public function test_it_returns_allowed_methods()
    {
        $request = self::createRequest("OPTIONS", "/users");
        $request = self::requestWithFailure($request, [
            "GET" => new Handler("Get"),
            "POST" => new Handler("Post"),
        ]);

        $factoryResponse = self::createResponse(200, "OK");

        $this->responseFactory->createResponse(200, "OK")->willReturn($factoryResponse);

        $response = $this->middleware->process($request, $this->nextHandler->reveal());

        $this->nextHandler->handle(Argument::any())->shouldNotHaveBeenCalled();

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals("OK", $response->getReasonPhrase());
        self::assertEquals(["GET, POST"], $response->getHeader("Options"));
    }
}
