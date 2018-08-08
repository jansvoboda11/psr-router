<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Integration;

use Nyholm\Psr7\Factory\Psr17Factory;
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

    public function test_with_match_uses_matched_handler()
    {
        $request = self::createRequest("POST", "/users");

        $postHandler = new Handler(self::createResponse(201));

        $routes = RouteCollection::create();
        $routes->post("/users", $postHandler);

        $dispatcher = self::createDispatcher($routes);

        $response = $dispatcher->dispatch($request);

        self::assertEquals(201, $response->getStatusCode());
    }

    public function test_invokes_automatic_options_middleware()
    {
        $request = self::createRequest("OPTIONS", "/users");

        $getHandler = new Handler(self::createResponse(202));
        $postHandler = new Handler(self::createResponse(203));

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

        $getHandler = new Handler(self::createResponse(201, "Created", "The GET response body."));

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

        $patchHandler = new Handler(self::createResponse(201));

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

        $responseFactory = new Psr17Factory();
        $streamFactory = new Psr17Factory();

        $handler = new Handler(self::createResponse(404));

        return new Dispatcher([
            new RouteMatchingMiddleware($router),
            new AutomaticOptionsMiddleware($responseFactory),
            new AutomaticHeadMiddleware($streamFactory),
            new MethodNotAllowedMiddleware($responseFactory),
            new RouteDispatchingMiddleware(),
        ], $handler);
    }
}
