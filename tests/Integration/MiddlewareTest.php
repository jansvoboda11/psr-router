<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Integration;

use Mockery;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Svoboda\Dispatcher\Dispatcher;
use Svoboda\Router\Middleware\AutomaticHeadMiddleware;
use Svoboda\Router\Middleware\AutomaticOptionsMiddleware;
use Svoboda\Router\Middleware\MethodNotAllowedMiddleware;
use Svoboda\Router\Middleware\RouteDispatchingMiddleware;
use Svoboda\Router\Middleware\RouteMatchingMiddleware;
use Svoboda\Router\RouteCollection;
use Svoboda\Router\Router;
use SvobodaTest\Router\TestCase;

class MiddlewareTest extends TestCase
{
    public function test_without_match_uses_default_handler()
    {
        $request = self::createRequest("GET", "/users");

        $routes = RouteCollection::create();

        $dispatcher = self::createDispatcher($routes);

        $response = $dispatcher->dispatch($request);

        self::assertEquals(404, $response->getStatusCode());
    }

    public function test_with_match_uses_matched_middleware()
    {
        $request = self::createRequest("POST", "/users");

        /** @var MockInterface|RequestHandlerInterface $usersHandler */
        $usersHandler = Mockery::mock(RequestHandlerInterface::class);
        $usersHandler
            ->shouldReceive("handle")
            ->andReturn(self::createResponse(201))
            ->once();

        $routes = RouteCollection::create();
        $routes->post("/users", $usersHandler);

        $dispatcher = self::createDispatcher($routes);

        $response = $dispatcher->dispatch($request);

        self::assertEquals(201, $response->getStatusCode());
    }

    public function test_invokes_automatic_options_middleware()
    {
        $request = self::createRequest("OPTIONS", "/users");

        /** @var MockInterface|RequestHandlerInterface $getHandler */
        $getHandler = Mockery::mock(RequestHandlerInterface::class);
        $getHandler->shouldNotReceive("handle");

        /** @var MockInterface|RequestHandlerInterface $postHandler */
        $postHandler = Mockery::mock(RequestHandlerInterface::class);
        $postHandler->shouldNotReceive("handle");

        $routes = RouteCollection::create();

        $routes->get("/users", $getHandler);
        $routes->post("/users", $postHandler);

        $dispatcher = self::createDispatcher($routes);

        $response = $dispatcher->dispatch($request);

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals(["GET, POST"], $response->getHeader("Options"));
    }

    public function test_invokes_automatic_head_middleware()
    {
        $request = self::createRequest("HEAD", "/users");

        /** @var MockInterface|RequestHandlerInterface $getHandler */
        $getHandler = Mockery::mock(RequestHandlerInterface::class);
        $getHandler
            ->shouldReceive("handle")
            ->andReturn(self::createResponse(201, "Created", "The GET response body."))
            ->once();

        $routes = RouteCollection::create();
        $routes->get("/users", $getHandler);

        $dispatcher = self::createDispatcher($routes);

        $response = $dispatcher->dispatch($request);

        self::assertEquals(201, $response->getStatusCode());
        self::assertEquals("", $response->getBody());
    }

    public function test_invokes_method_not_allowed_middleware()
    {
        $request = self::createRequest("POST", "/users");

        /** @var MockInterface|RequestHandlerInterface $patchHandler */
        $patchHandler = Mockery::mock(RequestHandlerInterface::class);
        $patchHandler->shouldNotReceive("handle");

        $routes = RouteCollection::create();
        $routes->patch("/users", $patchHandler);

        $dispatcher = self::createDispatcher($routes);

        $response = $dispatcher->dispatch($request);

        self::assertEquals(405, $response->getStatusCode());
        self::assertEquals("Method Not Allowed", $response->getReasonPhrase());
        self::assertEquals("PATCH", $response->getHeaderLine("Allow"));
    }

    /**
     * Create a full dispatcher with the given routes.
     *
     * @param RouteCollection $routes
     * @return Dispatcher
     */
    public static function createDispatcher(RouteCollection $routes): Dispatcher
    {
        $router = Router::create($routes);

        /** @var MockInterface|ResponseFactoryInterface $responseFactory */
        $responseFactory = Mockery::mock(ResponseFactoryInterface::class);
        $responseFactory
            ->shouldReceive("createResponse")
            ->with(200, "OK")
            ->andReturn(self::createResponse(200, "OK"));
        $responseFactory
            ->shouldReceive("createResponse")
            ->with(405, "Method Not Allowed")
            ->andReturn(self::createResponse(405, "Method Not Allowed"));

        /** @var MockInterface|StreamFactoryInterface $streamFactory */
        $streamFactory = Mockery::mock(StreamFactoryInterface::class);
        $streamFactory
            ->shouldReceive("createStream")
            ->andReturn(self::createStream());

        /** @var MockInterface|RequestHandlerInterface $handler */
        $handler = Mockery::mock(RequestHandlerInterface::class);
        $handler
            ->shouldReceive("handle")
            ->andReturn(self::createResponse(404, "Not Found", "Route does not exist."))
            ->atMost()
            ->once();

        return new Dispatcher([
            new RouteMatchingMiddleware($router),
            new AutomaticOptionsMiddleware($responseFactory),
            new AutomaticHeadMiddleware($streamFactory),
            new MethodNotAllowedMiddleware($responseFactory),
            new RouteDispatchingMiddleware(),
        ], $handler);
    }
}
