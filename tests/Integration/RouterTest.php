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
        $routes->get("/users", $handler);

        $match = Router::create($routes)->match($request);

        self::assertEquals($handler, $match->getHandler());
    }

    public function test_it_matches_second_from_two_static_routes()
    {
        $request = self::createRequest("GET", "/admins");

        $usersHandler = new Handler("Users");
        $adminsHandler = new Handler("Admins");

        $routes = RouteCollection::create();
        $routes->get("/users", $usersHandler);
        $routes->get("/admins", $adminsHandler);

        $match = Router::create($routes)->match($request);

        self::assertEquals($adminsHandler, $match->getHandler());
    }

    public function test_it_matches_first_from_two_ambiguous_routes()
    {
        $request = self::createRequest("GET", "/admins");

        $firstHandler = new Handler("Admins1");
        $secondHandler = new Handler("Admins2");

        $routes = RouteCollection::create();
        $routes->get("/admins", $firstHandler);
        $routes->get("/admins", $secondHandler);

        $match = Router::create($routes)->match($request);

        self::assertEquals($firstHandler, $match->getHandler());
    }

    public function test_it_matches_single_route_with_attributes()
    {
        $request = self::createRequest("GET", "/admins/jan/123");

        $handler = new Handler("Admins");

        $routes = RouteCollection::create();
        $routes->get("/admins/{name}/{id}", $handler);

        $match = Router::create($routes)->match($request);

        $matchRequest = $match->getRequest();

        self::assertEquals("jan", $matchRequest->getAttribute("name"));
        self::assertEquals("123", $matchRequest->getAttribute("id"));
        self::assertEquals($handler, $match->getHandler());
    }

    public function test_it_matches_second_from_two_routes_with_attributes()
    {
        $request = self::createRequest("GET", "/users/jan/123");

        $adminsHandler = new Handler("Admins");
        $usersHandler = new Handler("Users");

        $routes = RouteCollection::create();
        $routes->get("/admins/{name}/{id}", $adminsHandler);
        $routes->get("/users/{name}/{id}", $usersHandler);

        $match = Router::create($routes)->match($request);

        $matchRequest = $match->getRequest();

        self::assertEquals("jan", $matchRequest->getAttribute("name"));
        self::assertEquals("123", $matchRequest->getAttribute("id"));
        self::assertEquals($usersHandler, $match->getHandler());
    }

    public function test_it_matches_request_with_optional_attribute()
    {
        $request = self::createRequest("GET", "/users/jan");

        $handler = new Handler("Users");

        $routes = RouteCollection::create();
        $routes->get("/users/{name}[/{id}]", $handler);

        $match = Router::create($routes)->match($request);

        $matchRequest = $match->getRequest();

        self::assertEquals("jan", $matchRequest->getAttribute("name"));
        self::assertEquals(null, $matchRequest->getAttribute("id"));
        self::assertEquals($handler, $match->getHandler());
    }

    public function test_it_matches_based_on_request_method()
    {
        $request = self::createRequest("POST", "/users");

        $getHandler = new Handler("Get");
        $postHandler = new Handler("Post");

        $routes = RouteCollection::create();
        $routes->get("/users", $getHandler);
        $routes->post("/users", $postHandler);

        $match = Router::create($routes)->match($request);

        self::assertEquals($postHandler, $match->getHandler());
    }

    public function test_it_provides_allowed_methods_on_only_uri_match()
    {
        $request = self::createRequest("GET", "/users");

        $postHandler = new Handler("Post");
        $patchHandler = new Handler("Patch");
        $deleteHandler = new Handler("Delete");

        $routes = RouteCollection::create();
        $routes->post("/users", $postHandler);
        $routes->patch("/orders", $patchHandler);
        $routes->delete("/users", $deleteHandler);

        $failure = new Failure([
            "POST" => $postHandler,
            "DELETE" => $deleteHandler,
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
        $routes->get("/users", $handler);

        $match = Router::create($routes)->match($request);

        self::assertEquals($handler, $match->getHandler());
    }

    public function test_it_ignores_hash()
    {
        $request = self::createRequest("GET", "/users#main");

        $handler = new Handler("Users");

        $routes = RouteCollection::create();
        $routes->get("/users", $handler);

        $match = Router::create($routes)->match($request);

        self::assertEquals($handler, $match->getHandler());
    }
}
