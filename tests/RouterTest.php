<?php

declare(strict_types=1);

namespace SvobodaTest\PsrRouter;

use PHPUnit\Framework\TestCase;
use Svoboda\PsrRouter\Router;
use Svoboda\PsrRouter\RouteCollection;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Uri;

class RouterTest extends TestCase
{
    public function test_it_matches_single_static_route()
    {
        $request = $this->createGetRequest("/users");

        $routes = new RouteCollection();
        $routes->get("/users", "Users");

        $router = Router::create($routes);

        $match = $router->match($request);

        self::assertNotNull($match);
        self::assertEquals("Users", $match->getHandler());
    }

    public function test_it_matches_second_from_two_static_routes()
    {
        $request = $this->createGetRequest("/admins");

        $routes = new RouteCollection();
        $routes->get("/users", "Users");
        $routes->get("/admins", "Admins");

        $router = Router::create($routes);

        $match = $router->match($request);

        self::assertNotNull($match);
        self::assertEquals("Admins", $match->getHandler());
    }

    public function test_it_matches_first_from_two_ambiguous_routes()
    {
        $request = $this->createGetRequest("/admins");

        $routes = new RouteCollection();
        $routes->get("/admins", "Admins1");
        $routes->get("/admins", "Admins2");

        $router = Router::create($routes);

        $match = $router->match($request);

        self::assertNotNull($match);
        self::assertEquals("Admins1", $match->getHandler());
    }


    public function test_it_matches_single_route_with_attributes()
    {
        $request = $this->createGetRequest("/admins/jan/123");


        $routes = new RouteCollection();
        $routes->get("/admins/{name}/{id}", "Admins");

        $router = Router::create($routes);

        $match = $router->match($request);

        self::assertNotNull($match);
        self::assertEquals("Admins", $match->getHandler());
        self::assertEquals("jan", $match->getRequest()->getAttribute("name"));
        self::assertEquals("123", $match->getRequest()->getAttribute("id"));
    }

    public function test_it_matches_second_from_two_routes_with_attributes()
    {
        $request = $this->createGetRequest("/users/jan/123");

        $routes = new RouteCollection();
        $routes->get("/admins/{name}/{id}", "Admins");
        $routes->get("/users/{name}/{id}", "Users");

        $router = Router::create($routes);

        $match = $router->match($request);

        self::assertNotNull($match);
        self::assertEquals("Users", $match->getHandler());
        self::assertEquals("jan", $match->getRequest()->getAttribute("name"));
        self::assertEquals("123", $match->getRequest()->getAttribute("id"));
    }

    public function test_it_matches_request_with_optional_attribute()
    {
        $request = $this->createGetRequest("/users/jan");

        $routes = new RouteCollection();
        $routes->get("/users/{name}[/{id}]", "Users");

        $router = Router::create($routes);

        $match = $router->match($request);

        self::assertNotNull($match);
        self::assertEquals("Users", $match->getHandler());
        self::assertEquals("jan", $match->getRequest()->getAttribute("name"));
        self::assertEquals(null, $match->getRequest()->getAttribute("id"));
    }

    public function test_it_matches_based_on_request_method()
    {
        $request = $this->createPostRequest("/users");

        $routes = new RouteCollection();
        $routes->get("/users", "Get");
        $routes->post("/users", "Post");

        $router = Router::create($routes);

        $match = $router->match($request);

        self::assertNotNull($match);
        self::assertEquals("Post", $match->getHandler());
    }

    public function test_it_ignores_query_string()
    {
        $request = $this->createGetRequest("/users?key=value");

        $routes = new RouteCollection();
        $routes->get("/users", "Get");

        $router = Router::create($routes);

        $match = $router->match($request);

        self::assertNotNull($match);
        self::assertEquals("Get", $match->getHandler());
    }

    public function test_it_ignores_hash()
    {
        $request = $this->createGetRequest("/users#main");

        $routes = new RouteCollection();
        $routes->get("/users", "Get");

        $router = Router::create($routes);

        $match = $router->match($request);

        self::assertNotNull($match);
        self::assertEquals("Get", $match->getHandler());
    }

    public function test_it_does_not_match_route_with_extra_suffix()
    {
        $request = $this->createGetRequest("/users/jan/123");

        $routes = new RouteCollection();
        $routes->get("/users/{name}", "Users");

        $router = Router::create($routes);

        $match = $router->match($request);

        self::assertNull($match);
    }

    public function test_it_does_not_match_route_with_extra_prefix()
    {
        $request = $this->createGetRequest("/api/users/jan");

        $routes = new RouteCollection();
        $routes->get("/users/{name}", "Users");

        $router = Router::create($routes);

        $match = $router->match($request);

        self::assertNull($match);
    }

    private function createGetRequest(string $uri)
    {
        return (new ServerRequest())->withUri(new Uri($uri));
    }

    private function createPostRequest(string $uri)
    {
        return (new ServerRequest())->withMethod("POST")->withUri(new Uri($uri));
    }
}
