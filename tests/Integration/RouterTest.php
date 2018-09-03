<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Integration;

use Svoboda\Router\Compiler\Compiler;
use Svoboda\Router\Compiler\MultiPatternCompiler;
use Svoboda\Router\Compiler\Path\PathCodeFactory;
use Svoboda\Router\Compiler\Path\PathPatternFactory;
use Svoboda\Router\Compiler\Paths\TreeCodeFactory;
use Svoboda\Router\Compiler\PhpCodeCompiler;
use Svoboda\Router\Compiler\PhpCodeTreeCompiler;
use Svoboda\Router\Compiler\SinglePatternCompiler;
use Svoboda\Router\Compiler\Tree\TreeFactory;
use Svoboda\Router\Failure;
use Svoboda\Router\Route\Path\PathSerializer;
use Svoboda\Router\RouteCollection;
use Svoboda\Router\Router;
use SvobodaTest\Router\FakeHandler;
use SvobodaTest\Router\TestCase;

class RouterTest extends TestCase
{
    /**
     * @dataProvider getCompilers
     */
    public function test_it_matches_single_static_route(Compiler $compiler)
    {
        $request = self::createRequest("GET", "/users");

        $routes = RouteCollection::create();
        $route = $routes->get("/users", new FakeHandler());

        $match = (new Router($routes, $compiler))->match($request);

        self::assertEquals($route, $match->getRoute());
    }

    /**
     * @dataProvider getCompilers
     */
    public function test_it_matches_second_from_two_static_routes(Compiler $compiler)
    {
        $request = self::createRequest("GET", "/admins");

        $routes = RouteCollection::create();
        $usersRoute = $routes->get("/users", new FakeHandler());
        $adminsRoute = $routes->get("/admins", new FakeHandler());

        $match = (new Router($routes, $compiler))->match($request);

        self::assertEquals($adminsRoute, $match->getRoute());
    }

    /**
     * @dataProvider getCompilers
     */
    public function test_it_matches_first_from_two_ambiguous_routes(Compiler $compiler)
    {
        $request = self::createRequest("GET", "/admins");

        $routes = RouteCollection::create();
        $firstRoute = $routes->get("/admins", new FakeHandler());
        $secondRoute = $routes->get("/admins", new FakeHandler());

        $match = (new Router($routes, $compiler))->match($request);

        self::assertEquals($firstRoute, $match->getRoute());
    }

    /**
     * @dataProvider getCompilers
     */
    public function test_it_matches_single_route_with_attributes(Compiler $compiler)
    {
        $request = self::createRequest("GET", "/admins/jan/123");

        $routes = RouteCollection::create();
        $route = $routes->get("/admins/{name}/{id}", new FakeHandler());

        $match = (new Router($routes, $compiler))->match($request);

        $matchRequest = $match->getRequest();

        self::assertEquals("jan", $matchRequest->getAttribute("name"));
        self::assertEquals("123", $matchRequest->getAttribute("id"));
        self::assertEquals($route, $match->getRoute());
    }

    /**
     * @dataProvider getCompilers
     */
    public function test_it_matches_second_from_two_routes_with_attributes(Compiler $compiler)
    {
        $request = self::createRequest("GET", "/users/jan/123");

        $routes = RouteCollection::create();
        $adminsRoute = $routes->get("/admins/{name}/{id}", new FakeHandler());
        $usersRoute = $routes->get("/users/{name}/{id}", new FakeHandler());

        $match = (new Router($routes, $compiler))->match($request);

        $matchRequest = $match->getRequest();

        self::assertEquals("jan", $matchRequest->getAttribute("name"));
        self::assertEquals("123", $matchRequest->getAttribute("id"));
        self::assertEquals($usersRoute, $match->getRoute());
    }

    /**
     * @dataProvider getCompilers
     */
    public function test_it_matches_second_from_two_similar_routes(Compiler $compiler)
    {
        $request = self::createRequest("PATCH", "/admins/jan/svoboda/patch");

        $routes = RouteCollection::create();
        $postRoute = $routes->post("/admins/{name}/{id}/post", new FakeHandler());
        $patchRoute = $routes->patch("/admins/{name}/{surname}/patch", new FakeHandler());

        $match = (new Router($routes, $compiler))->match($request);

        $matchRequest = $match->getRequest();

        self::assertEquals("jan", $matchRequest->getAttribute("name"));
        self::assertEquals("svoboda", $matchRequest->getAttribute("surname"));
        self::assertEquals(null, $matchRequest->getAttribute("id"));
        self::assertEquals($patchRoute, $match->getRoute());
    }

    /**
     * @dataProvider getCompilers
     */
    public function test_it_matches_request_with_optional_attribute(Compiler $compiler)
    {
        $request = self::createRequest("GET", "/users/jan");

        $routes = RouteCollection::create();
        $route = $routes->get("/users/{name}[/{id}]", new FakeHandler());

        $match = (new Router($routes, $compiler))->match($request);

        $matchRequest = $match->getRequest();

        self::assertEquals("jan", $matchRequest->getAttribute("name"));
        self::assertEquals(null, $matchRequest->getAttribute("id"));
        self::assertEquals($route, $match->getRoute());
    }

    /**
     * @dataProvider getCompilers
     */
    public function test_it_matches_based_on_request_method(Compiler $compiler)
    {
        $request = self::createRequest("POST", "/users");

        $routes = RouteCollection::create();
        $getRoute = $routes->get("/users", new FakeHandler());
        $postRoute = $routes->post("/users", new FakeHandler());

        $match = (new Router($routes, $compiler))->match($request);

        self::assertEquals($postRoute, $match->getRoute());
    }

    /**
     * @dataProvider getCompilers
     */
    public function test_it_provides_allowed_methods_on_only_uri_match(Compiler $compiler)
    {
        $request = self::createRequest("GET", "/users");

        $routes = RouteCollection::create();
        $routes->post("/users", new FakeHandler());
        $routes->patch("/orders", new FakeHandler());
        $routes->delete("/users", new FakeHandler());

        $this->expectException(Failure::class);
        $this->expectExceptionMessage(
            "Failed to match incoming request, acceptable methods: [POST, DELETE]"
        );

        (new Router($routes, $compiler))->match($request);
    }

    /**
     * @dataProvider getCompilers
     */
    public function test_it_does_not_provide_allow_methods_on_no_uri_match(Compiler $compiler)
    {
        $request = self::createRequest("GET", "/orders");

        $routes = RouteCollection::create();
        $routes->post("/users", new FakeHandler());
        $routes->delete("/users", new FakeHandler());

        $this->expectException(Failure::class);
        $this->expectExceptionMessage("Failed to match incoming request, acceptable methods: []");

        (new Router($routes, $compiler))->match($request);
    }

    /**
     * @dataProvider getCompilers
     */
    public function test_it_does_not_match_route_with_extra_suffix(Compiler $compiler)
    {
        $request = self::createRequest("GET", "/users/jan/123");

        $routes = RouteCollection::create();
        $routes->get("/users/{name}", new FakeHandler());

        $this->expectException(Failure::class);
        $this->expectExceptionMessage("Failed to match incoming request, acceptable methods: []");

        (new Router($routes, $compiler))->match($request);
    }

    /**
     * @dataProvider getCompilers
     */
    public function test_it_does_not_match_route_with_extra_prefix(Compiler $compiler)
    {
        $request = self::createRequest("GET", "/api/users/jan");

        $routes = RouteCollection::create();
        $routes->get("/users/{name}", new FakeHandler());

        $this->expectException(Failure::class);
        $this->expectExceptionMessage("Failed to match incoming request, acceptable methods: []");

        (new Router($routes, $compiler))->match($request);
    }

    /**
     * @dataProvider getCompilers
     */
    public function test_it_ignores_query_string(Compiler $compiler)
    {
        $request = self::createRequest("GET", "/users?key=value");

        $routes = RouteCollection::create();
        $route = $routes->get("/users", new FakeHandler());

        $match = (new Router($routes, $compiler))->match($request);

        self::assertEquals($route, $match->getRoute());
    }

    /**
     * @dataProvider getCompilers
     */
    public function test_it_ignores_hash(Compiler $compiler)
    {
        $request = self::createRequest("GET", "/users#main");

        $routes = RouteCollection::create();
        $route = $routes->get("/users", new FakeHandler());

        $match = (new Router($routes, $compiler))->match($request);

        self::assertEquals($route, $match->getRoute());
    }

    /**
     * The data provider for various implementations of the Compiler interface.
     *
     * @return array
     */
    public function getCompilers()
    {
        return [
            "multi pattern" => [new MultiPatternCompiler(new PathPatternFactory())],
            "single pattern" => [new SinglePatternCompiler(new PathPatternFactory())],
            "linear code" => [new PhpCodeCompiler(new PathCodeFactory())],
            "tree code" => [new PhpCodeTreeCompiler(new TreeFactory(new PathSerializer()), new TreeCodeFactory())],
        ];
    }
}
