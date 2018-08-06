<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit;

use Mockery;
use Mockery\MockInterface;
use Svoboda\Router\Route\Path\StaticPath;
use Svoboda\Router\Route\Route;
use Svoboda\Router\Route\RouteFactory;
use Svoboda\Router\RouteCollection;
use SvobodaTest\Router\Handler;
use SvobodaTest\Router\TestCase;

class RouteCollectionTest extends TestCase
{
    /** @var MockInterface|RouteFactory */
    private $factory;

    /** @var RouteCollection */
    private $collection;

    protected function setUp()
    {
        $this->factory = Mockery::mock(RouteFactory::class);
        $this->collection = new RouteCollection($this->factory);
    }

    public function test_it_registers_get_route()
    {
        $path = new StaticPath("/users");
        $handler = new Handler("UsersAction");
        $route = new Route("GET", $path, $handler, []);

        $this->factory
            ->shouldReceive("create")
            ->with("GET", "/users", $handler, [])
            ->andReturn($route)
            ->once();

        $this->collection->get("/users", $handler, "users", []);

        $routes = $this->collection->all();

        self::assertCount(1, $routes);
        self::assertEquals($route, $routes[0]);
    }

    public function test_it_registers_post_route()
    {
        $path = new StaticPath("/users");
        $handler = new Handler("UsersAction");
        $route = new Route("POST", $path, $handler);

        $this->factory
            ->shouldReceive("create")
            ->with("POST", "/users", $handler, [])
            ->andReturn($route)
            ->once();

        $this->collection->post("/users", $handler, "users", []);

        $routes = $this->collection->all();

        self::assertCount(1, $routes);
        self::assertEquals($route, $routes[0]);
    }

    public function test_it_registers_put_route()
    {
        $path = new StaticPath("/users");
        $handler = new Handler("UsersAction");
        $route = new Route("PUT", $path, $handler);

        $this->factory
            ->shouldReceive("create")
            ->with("PUT", "/users", $handler, [])
            ->andReturn($route)
            ->once();

        $this->collection->put("/users", $handler, "users", []);

        $routes = $this->collection->all();

        self::assertCount(1, $routes);
        self::assertEquals($route, $routes[0]);
    }

    public function test_it_registers_patch_route()
    {
        $path = new StaticPath("/users");
        $handler = new Handler("UsersAction");
        $route = new Route("PATCH", $path, $handler, []);

        $this->factory
            ->shouldReceive("create")
            ->with("PATCH", "/users", $handler, [])
            ->andReturn($route)
            ->once();

        $this->collection->patch("/users", $handler, "users", []);

        $routes = $this->collection->all();

        self::assertCount(1, $routes);
        self::assertEquals($route, $routes[0]);
    }

    public function test_it_registers_delete_route()
    {
        $path = new StaticPath("/users");
        $handler = new Handler("UsersAction");
        $route = new Route("DELETE", $path, $handler, []);

        $this->factory
            ->shouldReceive("create")
            ->with("DELETE", "/users", $handler, [])
            ->andReturn($route)
            ->once();

        $this->collection->delete("/users", $handler, "users", []);

        $routes = $this->collection->all();

        self::assertCount(1, $routes);
        self::assertEquals($route, $routes[0]);
    }

    public function test_it_finds_named_route()
    {
        $path = new StaticPath("/users");
        $handler = new Handler("UsersAction");
        $route = new Route("GET", $path, $handler, []);

        $this->factory
            ->shouldReceive("create")
            ->with("GET", "/users", $handler, [])
            ->andReturn($route)
            ->once();

        $this->collection->get("/users", $handler, "users.all", []);

        $found = $this->collection->oneNamed("users.all");

        self::assertEquals($found, $route);
    }

    public function test_it_fails_to_find_named_route()
    {
        $path = new StaticPath("/users");
        $handler = new Handler("UsersAction");
        $route = new Route("GET", $path, $handler, []);

        $this->factory
            ->shouldReceive("create")
            ->with("GET", "/users", $handler, [])
            ->andReturn($route)
            ->once();

        $this->collection->get("/users", $handler, "users.all", []);

        $found = $this->collection->oneNamed("wrong.name");

        self::assertNull($found);
    }
}
