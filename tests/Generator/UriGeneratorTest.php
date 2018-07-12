<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Generator;

use Mockery;
use Svoboda\Router\Generator\RouteNotFound;
use Svoboda\Router\Generator\UriFactory;
use Svoboda\Router\Generator\UriGenerator;
use Svoboda\Router\Route\Path\StaticPath;
use Svoboda\Router\Route\Route;
use Svoboda\Router\RouteCollection;
use Svoboda\Router\Types\Types;
use SvobodaTest\Router\TestCase;

class UriGeneratorTest extends TestCase
{
    /** @var Types */
    private $types;

    protected function setUp()
    {
        $this->types = new Types([
            "any" => "[^/]+",
        ], "any");
    }

    public function test_it_fails_on_missing_route()
    {
        $routes = Mockery::mock(RouteCollection::class);
        $routes->shouldReceive("oneNamed")
            ->with("users.all")
            ->andReturn(null);

        $factory = Mockery::mock(UriFactory::class);

        $generator = new UriGenerator($routes, $factory, null);

        $this->expectException(RouteNotFound::class);

        $generator->generate("users.all", []);
    }

    public function test_creates_uri_without_prefix()
    {
        $path = new StaticPath("/users");
        $route = new Route("GET", $path, "UsersAction", $this->types);

        $routes = Mockery::mock(RouteCollection::class);
        $routes->shouldReceive("oneNamed")
            ->with("users.all")
            ->andReturn($route);

        $factory = Mockery::mock(UriFactory::class);
        $factory->shouldReceive("create")
            ->with($path, $this->types, [])
            ->andReturn("/users");

        $generator = new UriGenerator($routes, $factory, null);

        $uri = $generator->generate("users.all", []);

        self::assertEquals("/users", $uri);
    }

    public function test_it_creates_uri_with_prefix_from_constructor()
    {
        $path = new StaticPath("/users");
        $route = new Route("GET", $path, "UsersAction", $this->types);

        $routes = Mockery::mock(RouteCollection::class);
        $routes->shouldReceive("oneNamed")
            ->with("users.all")
            ->andReturn($route);

        $factory = Mockery::mock(UriFactory::class);
        $factory->shouldReceive("create")
            ->with($path, $this->types, [])
            ->andReturn("/users");

        $generator = new UriGenerator($routes, $factory, "/api");

        $uri = $generator->generate("users.all", []);

        self::assertEquals("/api/users", $uri);
    }

    public function test_method_prefix_overrides_constructor_prefix()
    {
        $path = new StaticPath("/users");
        $route = new Route("GET", $path, "UsersAction", $this->types);

        $routes = Mockery::mock(RouteCollection::class);
        $routes->shouldReceive("oneNamed")
            ->with("users.all")
            ->andReturn($route);

        $factory = Mockery::mock(UriFactory::class);
        $factory->shouldReceive("create")
            ->with($path, $this->types, [])
            ->andReturn("/users");

        $generator = new UriGenerator($routes, $factory, "/api");

        $uri = $generator->generate("users.all", [], "/web");

        self::assertEquals("/web/users", $uri);
    }
}
