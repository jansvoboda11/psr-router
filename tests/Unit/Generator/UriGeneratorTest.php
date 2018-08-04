<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit\Generator;

use Mockery;
use Mockery\MockInterface;
use Svoboda\Router\Generator\RouteNotFound;
use Svoboda\Router\Generator\UriFactory;
use Svoboda\Router\Generator\UriGenerator;
use Svoboda\Router\Route\Path\StaticPath;
use Svoboda\Router\Route\Route;
use Svoboda\Router\RouteCollection;
use Svoboda\Router\Types\Types;
use SvobodaTest\Router\Handler;
use SvobodaTest\Router\TestCase;

class UriGeneratorTest extends TestCase
{
    /** @var Types */
    private $types;

    /** @var MockInterface|RouteCollection */
    private $routes;

    /** @var MockInterface|UriFactory */
    private $factory;

    protected function setUp()
    {
        $this->types = new Types([
            "any" => "[^/]+",
        ], "any");

        $this->routes = Mockery::mock(RouteCollection::class);
        $this->factory = Mockery::mock(UriFactory::class);
    }

    public function test_it_fails_on_missing_route()
    {
        $this->routes
            ->shouldReceive("oneNamed")
            ->with("users.all")
            ->andReturn(null);

        $generator = new UriGenerator($this->routes, $this->factory, null);

        $this->expectException(RouteNotFound::class);

        $generator->generate("users.all", []);
    }

    public function test_creates_uri_without_prefix()
    {
        $path = new StaticPath("/users");
        $route = new Route("GET", $path, new Handler("UsersAction"), $this->types);

        $this->routes
            ->shouldReceive("oneNamed")
            ->with("users.all")
            ->andReturn($route);

        $this->factory
            ->shouldReceive("create")
            ->with($path, $this->types, [])
            ->andReturn("/users");

        $generator = new UriGenerator($this->routes, $this->factory, null);

        $uri = $generator->generate("users.all", []);

        self::assertEquals("/users", $uri);
    }

    public function test_it_creates_uri_with_prefix_from_constructor()
    {
        $path = new StaticPath("/users");
        $route = new Route("GET", $path, new Handler("UsersAction"), $this->types);

        $this->routes
            ->shouldReceive("oneNamed")
            ->with("users.all")
            ->andReturn($route);

        $this->factory
            ->shouldReceive("create")
            ->with($path, $this->types, [])
            ->andReturn("/users");

        $generator = new UriGenerator($this->routes, $this->factory, "/api");

        $uri = $generator->generate("users.all", []);

        self::assertEquals("/api/users", $uri);
    }

    public function test_method_prefix_overrides_constructor_prefix()
    {
        $path = new StaticPath("/users");
        $route = new Route("GET", $path, new Handler("UsersAction"), $this->types);

        $this->routes
            ->shouldReceive("oneNamed")
            ->with("users.all")
            ->andReturn($route);

        $this->factory
            ->shouldReceive("create")
            ->with($path, $this->types, [])
            ->andReturn("/users");

        $generator = new UriGenerator($this->routes, $this->factory, "/api");

        $uri = $generator->generate("users.all", [], "/web");

        self::assertEquals("/web/users", $uri);
    }
}
