<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Middleware;

use Hamcrest\Matchers;
use Mockery;
use Mockery\MockInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Svoboda\Router\Failure;
use Svoboda\Router\Match;
use Svoboda\Router\Middleware\AutomaticHeadMiddleware;
use Svoboda\Router\Router;
use SvobodaTest\Router\Middleware;
use SvobodaTest\Router\TestCase;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\JsonResponse;

class AutomaticHeadMiddlewareTest extends TestCase
{
    /** @var MockInterface|Router */
    private $router;

    /** @var MockInterface|RequestHandlerInterface */
    private $handler;

    /** @var StreamInterface */
    private $emptyBody;

    /** @var AutomaticHeadMiddleware */
    private $middleware;

    protected function setUp()
    {
        $this->router = Mockery::mock(Router::class);
        $this->handler = Mockery::mock(RequestHandlerInterface::class);
        $this->emptyBody = self::createStream();
        $this->middleware = new AutomaticHeadMiddleware($this->router, $this->emptyBody);
    }

    public function test_it_ignores_non_head_request()
    {
        $request = self::createRequest("POST", "/users");

        $handlerResponse = new JsonResponse(["foo" => "bar"]);

        $this->router->shouldNotReceive("match");
        $this->handler
            ->shouldReceive("handle")
            ->with($request)
            ->andReturn($handlerResponse)
            ->once();

        $response = $this->middleware->process($request, $this->handler);

        self::assertEquals($response, $handlerResponse);
    }

    public function test_it_ignores_matched_head_route()
    {
        $request = self::createRequest("HEAD", "/users");
        $match = new Match(new Middleware("Users"), $request);
        $request = $request->withAttribute(Match::class, $match);

        $handlerResponse = (new Response())->withHeader("Allow", "GET, POST");

        $this->router->shouldNotReceive("match");
        $this->handler
            ->shouldReceive("handle")
            ->with($request)
            ->andReturn($handlerResponse)
            ->once();

        $response = $this->middleware->process($request, $this->handler);

        self::assertEquals($response, $handlerResponse);
    }

    public function test_it_ignores_uri_failure()
    {
        $request = self::createRequest("HEAD", "/users");
        $failure = new Failure([], $request);
        $request = $request->withAttribute(Failure::class, $failure);

        $handlerResponse = (new Response())->withStatus(404, "Not Found");

        $this->router->shouldNotReceive("match");
        $this->handler
            ->shouldReceive("handle")
            ->with($request)
            ->andReturn($handlerResponse)
            ->once();

        $response = $this->middleware->process($request, $this->handler);

        self::assertEquals($response, $handlerResponse);
    }

    public function test_it_ignores_when_get_is_missing()
    {
        $request = self::createRequest("HEAD", "/users");
        $failure = new Failure(["POST"], $request);
        $request = $request->withAttribute(Failure::class, $failure);

        $handlerResponse = (new Response())->withStatus(404, "Not Found");

        $this->router->shouldNotReceive("match");
        $this->handler
            ->shouldReceive("handle")
            ->with($request)
            ->andReturn($handlerResponse)
            ->once();

        $response = $this->middleware->process($request, $this->handler);

        self::assertEquals($response, $handlerResponse);
    }

    public function test_it_returns_get_response_with_empty_body()
    {
        $request = self::createRequest("HEAD", "/users");

        $failure = new Failure(["GET"], $request);
        $failureRequest = $request->withAttribute(Failure::class, $failure);

        $getRequest = $request->withMethod("GET");

        $match = new Match(new Middleware("Get"), $request);
        $matchRequest = $getRequest->withAttribute(Match::class, $match);

        $getResponse = new JsonResponse(["foo" => "bar"]);
        $headResponse = $getResponse->withBody($this->emptyBody);

        $this->router
            ->shouldReceive("match")
            ->with(Matchers::equalTo($getRequest))
            ->andReturn($match)
            ->once();
        $this->handler
            ->shouldReceive("handle")
            ->with(Matchers::equalTo($matchRequest))
            ->andReturn($getResponse)
            ->once();

        $response = $this->middleware->process($failureRequest, $this->handler);

        self::assertEquals($response, $headResponse);
    }
}
