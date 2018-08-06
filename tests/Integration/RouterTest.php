<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Integration;

use Svoboda\Router\Failure;
use Svoboda\Router\RouteCollection;
use Svoboda\Router\Router;
use SvobodaTest\Router\Handler;
use SvobodaTest\Router\TestCase;

class RouterTest extends TestCase
{
    public function test_it_matches_single_static_route()
    {
        $request = self::createRequest("GET", "/users");
        $handler = new Handler("Users");

        $routes = RouteCollection::create();
        $route = $routes->get("/users", $handler);

        $match = Router::create($routes)->match($request);

        self::assertEquals($route, $match->getRoute());
    }

    public function test_it_matches_second_from_two_static_routes()
    {
        $request = self::createRequest("GET", "/admins");

        $usersHandler = new Handler("Users");
        $adminsHandler = new Handler("Admins");

        $routes = RouteCollection::create();
        $usersRoute = $routes->get("/users", $usersHandler);
        $adminsRoute = $routes->get("/admins", $adminsHandler);

        $match = Router::create($routes)->match($request);

        self::assertEquals($adminsRoute, $match->getRoute());
    }

    public function test_it_matches_first_from_two_ambiguous_routes()
    {
        $request = self::createRequest("GET", "/admins");

        $firstHandler = new Handler("Admins1");
        $secondHandler = new Handler("Admins2");

        $routes = RouteCollection::create();
        $firstRoute = $routes->get("/admins", $firstHandler);
        $secondRoute = $routes->get("/admins", $secondHandler);

        $match = Router::create($routes)->match($request);

        self::assertEquals($firstRoute, $match->getRoute());
    }

    public function test_it_matches_single_route_with_attributes()
    {
        $request = self::createRequest("GET", "/admins/jan/123");

        $handler = new Handler("Admins");

        $routes = RouteCollection::create();
        $route = $routes->get("/admins/{name}/{id}", $handler);

        $match = Router::create($routes)->match($request);

        $matchRequest = $match->getRequest();

        self::assertEquals("jan", $matchRequest->getAttribute("name"));
        self::assertEquals("123", $matchRequest->getAttribute("id"));
        self::assertEquals($route, $match->getRoute());
    }

    public function test_it_matches_second_from_two_routes_with_attributes()
    {
        $request = self::createRequest("GET", "/users/jan/123");

        $adminsHandler = new Handler("Admins");
        $usersHandler = new Handler("Users");

        $routes = RouteCollection::create();
        $adminsRoute = $routes->get("/admins/{name}/{id}", $adminsHandler);
        $usersRoute = $routes->get("/users/{name}/{id}", $usersHandler);

        $match = Router::create($routes)->match($request);

        $matchRequest = $match->getRequest();

        self::assertEquals("jan", $matchRequest->getAttribute("name"));
        self::assertEquals("123", $matchRequest->getAttribute("id"));
        self::assertEquals($usersRoute, $match->getRoute());
    }

    public function test_it_matches_request_with_optional_attribute()
    {
        $request = self::createRequest("GET", "/users/jan");

        $handler = new Handler("Users");

        $routes = RouteCollection::create();
        $route = $routes->get("/users/{name}[/{id}]", $handler);

        $match = Router::create($routes)->match($request);

        $matchRequest = $match->getRequest();

        self::assertEquals("jan", $matchRequest->getAttribute("name"));
        self::assertEquals(null, $matchRequest->getAttribute("id"));
        self::assertEquals($route, $match->getRoute());
    }

    public function test_it_matches_based_on_request_method()
    {
        $request = self::createRequest("POST", "/users");

        $getHandler = new Handler("Get");
        $postHandler = new Handler("Post");

        $routes = RouteCollection::create();
        $getRoute = $routes->get("/users", $getHandler);
        $postRoute = $routes->post("/users", $postHandler);

        $match = Router::create($routes)->match($request);

        self::assertEquals($postRoute, $match->getRoute());
    }

    public function test_it_provides_allowed_methods_on_only_uri_match()
    {
        $request = self::createRequest("GET", "/users");

        $postHandler = new Handler("Post");
        $patchHandler = new Handler("Patch");
        $deleteHandler = new Handler("Delete");

        $routes = RouteCollection::create();
        $postRoute = $routes->post("/users", $postHandler);
        $patchRoute = $routes->patch("/orders", $patchHandler);
        $deleteRoute = $routes->delete("/users", $deleteHandler);

        $failure = new Failure([
            "POST" => $postRoute,
            "DELETE" => $deleteRoute,
        ], $request);

        $this->expectThrowable($failure);

        Router::create($routes)->match($request);
    }

    public function test_it_does_not_provide_allow_methods_on_no_uri_match()
    {
        $request = self::createRequest("GET", "/orders");

        $postHandler = new Handler("Post");
        $deleteHandler = new Handler("Delete");

        $routes = RouteCollection::create();
        $routes->post("/users", $postHandler);
        $routes->delete("/users", $deleteHandler);

        $failure = new Failure([], $request);

        $this->expectThrowable($failure);

        Router::create($routes)->match($request);
    }

    public function test_it_does_not_match_route_with_extra_suffix()
    {
        $request = self::createRequest("GET", "/users/jan/123");

        $handler = new Handler("Users");

        $routes = RouteCollection::create();
        $routes->get("/users/{name}", $handler);

        $failure = new Failure([], $request);

        $this->expectThrowable($failure);

        Router::create($routes)->match($request);
    }

    public function test_it_does_not_match_route_with_extra_prefix()
    {
        $request = self::createRequest("GET", "/api/users/jan");

        $handler = new Handler("Users");

        $routes = RouteCollection::create();
        $routes->get("/users/{name}", $handler);

        $failure = new Failure([], $request);

        $this->expectThrowable($failure);

        Router::create($routes)->match($request);
    }

    public function test_it_ignores_query_string()
    {
        $request = self::createRequest("GET", "/users?key=value");

        $handler = new Handler("Users");

        $routes = RouteCollection::create();
        $route = $routes->get("/users", $handler);

        $match = Router::create($routes)->match($request);

        self::assertEquals($route, $match->getRoute());
    }

    public function test_it_ignores_hash()
    {
        $request = self::createRequest("GET", "/users#main");

        $handler = new Handler("Users");

        $routes = RouteCollection::create();
        $route = $routes->get("/users", $handler);

        $match = Router::create($routes)->match($request);

        self::assertEquals($route, $match->getRoute());
    }
}
