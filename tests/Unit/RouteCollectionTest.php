<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit;

use Mockery;
use Mockery\MockInterface;
use Svoboda\Router\Route\Path\StaticPath;
use Svoboda\Router\Route\Route;
use Svoboda\Router\Route\RouteFactory;
use Svoboda\Router\RouteCollection;
use Svoboda\Router\Types\Types;
use SvobodaTest\Router\Handler;
use SvobodaTest\Router\TestCase;

class RouteCollectionTest extends TestCase
{
    /** @var Types */
    private $types;

    /** @var MockInterface|RouteFactory */
    private $factory;

    /** @var RouteCollection */
    private $collection;

    protected function setUp()
    {
        $this->types = new Types([
            "any" => "[^/]+",
        ], "any");

        $this->factory = Mockery::mock(RouteFactory::class);
        $this->collection = new RouteCollection($this->factory, $this->types);
    }

    public function test_it_registers_route_without_name()
    {
        $path = new StaticPath("/users");
        $handler = new Handler("UsersAction");
        $route = new Route("GET", $path, $handler, $this->types);

        $this->factory
            ->shouldReceive("create")
            ->with("GET", "/users", $handler, $this->types)
            ->andReturn($route)
            ->once();

        $this->collection->get("/users", $handler);

        $routes = $this->collection->all();

        self::assertCount(1, $routes);
        self::assertEquals($route, $routes[0]);
    }

    public function test_it_registers_route_with_name()
    {
        $path = new StaticPath("/users");
        $handler = new Handler("UsersAction");
        $route = new Route("GET", $path, $handler, $this->types);

        $this->factory
            ->shouldReceive("create")
            ->with("GET", "/users", $handler, $this->types)
            ->andReturn($route)
            ->once();

        $this->collection->get("/users", $handler, "users.all");

        $routes = $this->collection->all();

        self::assertCount(1, $routes);
        self::assertEquals($route, $routes[0]);
    }

    public function test_it_finds_named_route()
    {
        $path = new StaticPath("/users");
        $handler = new Handler("UsersAction");
        $route = new Route("GET", $path, $handler, $this->types);

        $this->factory
            ->shouldReceive("create")
            ->with("GET", "/users", $handler, $this->types)
            ->andReturn($route)
            ->once();

        $this->collection->get("/users", $handler, "users.all");

        $found = $this->collection->oneNamed("users.all");

        self::assertEquals($found, $route);
    }

    public function test_it_fails_to_find_named_route()
    {
        $path = new StaticPath("/users");
        $handler = new Handler("UsersAction");
        $route = new Route("GET", $path, $handler, $this->types);

        $this->factory
            ->shouldReceive("create")
            ->with("GET", "/users", $handler, $this->types)
            ->andReturn($route)
            ->once();

        $this->collection->get("/users", $handler, "users.all");

        $found = $this->collection->oneNamed("wrong.name");

        self::assertNull($found);
    }
}
