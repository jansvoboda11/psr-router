<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit\Middleware;

use Mockery;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Svoboda\Router\Failure;
use Svoboda\Router\Match;
use Svoboda\Router\Middleware\AutomaticOptionsMiddleware;
use SvobodaTest\Router\Middleware;
use SvobodaTest\Router\TestCase;
use Zend\Diactoros\Response\JsonResponse;

class AutomaticOptionsMiddlewareTest extends TestCase
{
    /** @var MockInterface|RequestHandlerInterface */
    private $handler;

    /** @var ResponseInterface */
    private $emptyResponse;

    /** @var AutomaticOptionsMiddleware */
    private $middleware;

    protected function setUp()
    {
        $this->handler = Mockery::mock(RequestHandlerInterface::class);
        $this->emptyResponse = self::createResponse();
        $this->middleware = new AutomaticOptionsMiddleware($this->emptyResponse);
    }

    public function test_it_ignores_non_options_request()
    {
        $request = self::createRequest("GET", "/users");

        $handlerResponse = new JsonResponse(["foo" => "bar"]);

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
        $request = self::createRequest("OPTIONS", "/users");
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
        $request = self::createRequest("OPTIONS", "/users");
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

    public function test_it_returns_allowed_methods()
    {
        $request = self::createRequest("OPTIONS", "/users");
        $failure = new Failure(["GET", "POST"], $request);
        $request = $request->withAttribute(Failure::class, $failure);

        $optionsResponse = $this->emptyResponse->withHeader("Options", "GET, POST");

        $this->handler->shouldNotReceive("handle");

        $response = $this->middleware->process($request, $this->handler);

        self::assertEquals($optionsResponse, $response);
    }
}
