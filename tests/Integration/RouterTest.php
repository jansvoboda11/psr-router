<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Integration;

use Svoboda\Router\Failure;
use Svoboda\Router\RouteCollection;
use Svoboda\Router\Router;
use SvobodaTest\Router\Middleware;
use SvobodaTest\Router\TestCase;

class RouterTest extends TestCase
{
    public function test_it_matches_single_static_route()
    {
        $request = self::createRequest("GET", "/users");

        $routes = RouteCollection::create();
        $routes->get("/users", new Middleware("Users"));

        $match = Router::create($routes)->match($request);

        self::assertEquals(new Middleware("Users"), $match->getMiddleware());
    }

    public function test_it_matches_second_from_two_static_routes()
    {
        $request = self::createRequest("GET", "/admins");

        $routes = RouteCollection::create();
        $routes->get("/users", new Middleware("Users"));
        $routes->get("/admins", new Middleware("Admins"));

        $match = Router::create($routes)->match($request);

        self::assertEquals(new Middleware("Admins"), $match->getMiddleware());
    }

    public function test_it_matches_first_from_two_ambiguous_routes()
    {
        $request = self::createRequest("GET", "/admins");

        $routes = RouteCollection::create();
        $routes->get("/admins", new Middleware("Admins1"));
        $routes->get("/admins", new Middleware("Admins2"));

        $match = Router::create($routes)->match($request);

        self::assertEquals(new Middleware("Admins1"), $match->getMiddleware());
    }

    public function test_it_matches_single_route_with_attributes()
    {
        $request = self::createRequest("GET", "/admins/jan/123");

        $routes = RouteCollection::create();
        $routes->get("/admins/{name}/{id}", new Middleware("Admins"));

        $match = Router::create($routes)->match($request);

        self::assertEquals(new Middleware("Admins"), $match->getMiddleware());
        self::assertEquals("jan", $match->getRequest()->getAttribute("name"));
        self::assertEquals("123", $match->getRequest()->getAttribute("id"));
    }

    public function test_it_matches_second_from_two_routes_with_attributes()
    {
        $request = self::createRequest("GET", "/users/jan/123");

        $routes = RouteCollection::create();
        $routes->get("/admins/{name}/{id}", new Middleware("Admins"));
        $routes->get("/users/{name}/{id}", new Middleware("Users"));

        $match = Router::create($routes)->match($request);

        self::assertEquals(new Middleware("Users"), $match->getMiddleware());
        self::assertEquals("jan", $match->getRequest()->getAttribute("name"));
        self::assertEquals("123", $match->getRequest()->getAttribute("id"));
    }

    public function test_it_matches_request_with_optional_attribute()
    {
        $request = self::createRequest("GET", "/users/jan");

        $routes = RouteCollection::create();
        $routes->get("/users/{name}[/{id}]", new Middleware("Users"));

        $match = Router::create($routes)->match($request);

        self::assertEquals(new Middleware("Users"), $match->getMiddleware());
        self::assertEquals("jan", $match->getRequest()->getAttribute("name"));
        self::assertEquals(null, $match->getRequest()->getAttribute("id"));
    }

    public function test_it_matches_based_on_request_method()
    {
        $request = self::createRequest("POST", "/users");

        $routes = RouteCollection::create();
        $routes->get("/users", new Middleware("Get"));
        $routes->post("/users", new Middleware("Post"));

        $match = Router::create($routes)->match($request);

        self::assertEquals(new Middleware("Post"), $match->getMiddleware());
    }

    public function test_it_provides_allowed_methods_on_only_uri_match()
    {
        $request = self::createRequest("GET", "/users");

        $routes = RouteCollection::create();
        $routes->post("/users", new Middleware("Post"));
        $routes->patch("/orders", new Middleware("Patch"));
        $routes->delete("/users", new Middleware("Delete"));

        $failure = new Failure(["POST", "DELETE"], $request);

        $this->expectThrowable($failure);

        Router::create($routes)->match($request);
    }

    public function test_it_does_not_provide_allow_methods_on_no_uri_match()
    {
        $request = self::createRequest("GET", "/orders");

        $routes = RouteCollection::create();
        $routes->post("/users", new Middleware("Post"));
        $routes->delete("/users", new Middleware("Delete"));

        $failure = new Failure([], $request);

        $this->expectThrowable($failure);

        Router::create($routes)->match($request);
    }

    public function test_it_does_not_match_route_with_extra_suffix()
    {
        $request = self::createRequest("GET", "/users/jan/123");

        $routes = RouteCollection::create();
        $routes->get("/users/{name}", new Middleware("Users"));

        $failure = new Failure([], $request);

        $this->expectThrowable($failure);

        Router::create($routes)->match($request);
    }

    public function test_it_does_not_match_route_with_extra_prefix()
    {
        $request = self::createRequest("GET", "/api/users/jan");

        $routes = RouteCollection::create();
        $routes->get("/users/{name}", new Middleware("Users"));

        $failure = new Failure([], $request);

        $this->expectThrowable($failure);

        Router::create($routes)->match($request);
    }

    public function test_it_ignores_query_string()
    {
        $request = self::createRequest("GET", "/users?key=value");

        $routes = RouteCollection::create();
        $routes->get("/users", new Middleware("Users"));

        $match = Router::create($routes)->match($request);

        self::assertEquals(new Middleware("Users"), $match->getMiddleware());
    }

    public function test_it_ignores_hash()
    {
        $request = self::createRequest("GET", "/users#main");

        $routes = RouteCollection::create();
        $routes->get("/users", new Middleware("Users"));

        $match = Router::create($routes)->match($request);

        self::assertEquals(new Middleware("Users"), $match->getMiddleware());
    }
}
