<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit;

use Prophecy\Prophecy\ObjectProphecy;
use Svoboda\Router\Route\Path\StaticPath;
use Svoboda\Router\Route\Route;
use Svoboda\Router\Route\RouteFactory;
use Svoboda\Router\RouteCollection;
use SvobodaTest\Router\FakeHandler;
use SvobodaTest\Router\TestCase;

class RouteCollectionTest extends TestCase
{
    /** @var ObjectProphecy|RouteFactory */
    private $factory;

    /** @var RouteCollection */
    private $collection;

    protected function setUp()
    {
        $this->factory = $this->prophesize(RouteFactory::class);
        $this->collection = new RouteCollection($this->factory->reveal());
    }

    public function test_it_registers_get_route()
    {
        $path = new StaticPath("/users");
        $handler = new FakeHandler();
        $route = new Route("GET", $path, $handler, "users", []);

        $this->factory->create("GET", "/users", $handler, "users", [])->willReturn($route);

        $this->collection->get("/users", $handler, "users", []);

        $routes = $this->collection->all();

        self::assertEquals([$route], $routes);
    }

    public function test_it_registers_post_route()
    {
        $path = new StaticPath("/users");
        $handler = new FakeHandler();
        $route = new Route("POST", $path, $handler, "users");

        $this->factory->create("POST", "/users", $handler, "users", [])->willReturn($route);

        $this->collection->post("/users", $handler, "users", []);

        $routes = $this->collection->all();

        self::assertEquals([$route], $routes);
    }

    public function test_it_registers_put_route()
    {
        $path = new StaticPath("/users");
        $handler = new FakeHandler();
        $route = new Route("PUT", $path, $handler, "users", []);

        $this->factory->create("PUT", "/users", $handler, "users", [])->willReturn($route);

        $this->collection->put("/users", $handler, "users", []);

        $routes = $this->collection->all();

        self::assertEquals([$route], $routes);
    }

    public function test_it_registers_patch_route()
    {
        $path = new StaticPath("/users");
        $handler = new FakeHandler();
        $route = new Route("PATCH", $path, $handler, "users", []);

        $this->factory->create("PATCH", "/users", $handler, "users", [])->willReturn($route);

        $this->collection->patch("/users", $handler, "users", []);

        $routes = $this->collection->all();

        self::assertEquals([$route], $routes);
    }

    public function test_it_registers_delete_route()
    {
        $path = new StaticPath("/users");
        $handler = new FakeHandler();
        $route = new Route("DELETE", $path, $handler, "users", []);

        $this->factory->create("DELETE", "/users", $handler, "users", [])->willReturn($route);

        $this->collection->delete("/users", $handler, "users", []);

        $routes = $this->collection->all();

        self::assertEquals([$route], $routes);
    }

    public function test_it_finds_named_route()
    {
        $path = new StaticPath("/users");
        $handler = new FakeHandler();
        $route = new Route("GET", $path, $handler, "users.all", []);

        $this->factory->create("GET", "/users", $handler, "users.all", [])->willReturn($route);

        $this->collection->get("/users", $handler, "users.all", []);

        $found = $this->collection->oneNamed("users.all");

        self::assertEquals($found, $route);
    }

    public function test_it_fails_to_find_named_route()
    {
        $path = new StaticPath("/users");
        $handler = new FakeHandler();
        $route = new Route("GET", $path, $handler, "users.all", []);

        $this->factory->create("GET", "/users", $handler, "users.all", [])->willReturn($route);

        $this->collection->get("/users", $handler, "users.all", []);

        $found = $this->collection->oneNamed("wrong.name");

        self::assertNull($found);
    }
}
