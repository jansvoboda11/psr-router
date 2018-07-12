<?php

namespace SvobodaTest\Router;

use Mockery;
use Svoboda\Router\Route\Path\StaticPath;
use Svoboda\Router\Route\Route;
use Svoboda\Router\Route\RouteFactory;
use Svoboda\Router\RouteCollection;
use Svoboda\Router\Types\Types;

class RouteCollectionTest extends TestCase
{
    /** @var Types */
    private $types;

    protected function setUp()
    {
        $this->types = new Types([
            "any" => "[^/]+",
        ], "any");
    }

    public function test_it_registers_route_without_name()
    {
        $path = new StaticPath("/users");
        $route = new Route("GET", $path, "UsersAction", $this->types);

        $factory = Mockery::mock(RouteFactory::class);
        $factory->shouldReceive("create")
            ->with("GET", "/users", "UsersAction", $this->types)
            ->andReturn($route)
            ->once();

        $collection = new RouteCollection($factory, $this->types);

        $collection->get("/users", "UsersAction");

        $routes = $collection->all();

        self::assertCount(1, $routes);
        self::assertEquals($route, $routes[0]);
    }

    public function test_it_registers_route_with_name()
    {
        $path = new StaticPath("/users");
        $route = new Route("GET", $path, "UsersAction", $this->types);

        $factory = Mockery::mock(RouteFactory::class);
        $factory->shouldReceive("create")
            ->with("GET", "/users", "UsersAction", $this->types)
            ->andReturn($route)
            ->once();

        $collection = new RouteCollection($factory, $this->types);

        $collection->get("/users", "UsersAction", "users.all");

        $routes = $collection->all();

        self::assertCount(1, $routes);
        self::assertEquals($route, $routes[0]);
    }

    public function test_it_finds_named_route()
    {
        $path = new StaticPath("/users");
        $route = new Route("GET", $path, "UsersAction", $this->types);

        $factory = Mockery::mock(RouteFactory::class);
        $factory->shouldReceive("create")
            ->with("GET", "/users", "UsersAction", $this->types)
            ->andReturn($route)
            ->once();

        $collection = new RouteCollection($factory, $this->types);

        $collection->get("/users", "UsersAction", "users.all");

        $found = $collection->oneNamed("users.all");

        self::assertEquals($found, $route);
    }

    public function test_it_fails_to_find_named_route()
    {
        $path = new StaticPath("/users");
        $route = new Route("GET", $path, "UsersAction", $this->types);

        $factory = Mockery::mock(RouteFactory::class);
        $factory->shouldReceive("create")
            ->with("GET", "/users", "UsersAction", $this->types)
            ->andReturn($route)
            ->once();

        $collection = new RouteCollection($factory, $this->types);

        $collection->get("/users", "UsersAction", "users.all");

        $found = $collection->oneNamed("wrong.name");

        self::assertNull($found);
    }
}
