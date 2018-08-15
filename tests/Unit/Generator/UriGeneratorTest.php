<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit\Generator;

use Prophecy\Prophecy\ObjectProphecy;
use Svoboda\Router\Generator\RouteNotFound;
use Svoboda\Router\Generator\UriFactory;
use Svoboda\Router\Generator\UriGenerator;
use Svoboda\Router\Route\Path\StaticPath;
use Svoboda\Router\Route\Route;
use Svoboda\Router\RouteCollection;
use Svoboda\Router\Types\TypeCollection;
use SvobodaTest\Router\Handler;
use SvobodaTest\Router\TestCase;

class UriGeneratorTest extends TestCase
{
    /** @var TypeCollection */
    private $types;

    /** @var ObjectProphecy|RouteCollection */
    private $routes;

    /** @var ObjectProphecy|UriFactory */
    private $factory;

    protected function setUp()
    {
        $this->types = TypeCollection::createDefault();

        $this->routes = $this->prophesize(RouteCollection::class);
        $this->factory = $this->prophesize(UriFactory::class);
    }

    public function test_static_constructor_works()
    {
        $generator = UriGenerator::create($this->routes->reveal());

        self::assertInstanceOf(UriGenerator::class, $generator);
    }

    public function test_it_fails_on_missing_route()
    {
        $this->routes->oneNamed("users.all")->willReturn(null);

        $generator = new UriGenerator($this->routes->reveal(), $this->factory->reveal(), null);

        $this->expectException(RouteNotFound::class);

        $generator->generate("users.all", []);
    }

    public function test_creates_uri_without_prefix()
    {
        $path = new StaticPath("/users");
        $handler = new Handler("UsersAction");
        $route = new Route("GET", $path, $handler);

        $this->routes->oneNamed("users.all")->willReturn($route);

        $this->factory->create($path, [])->willReturn("/users");

        $generator = new UriGenerator($this->routes->reveal(), $this->factory->reveal(), null);

        $uri = $generator->generate("users.all", []);

        self::assertEquals("/users", $uri);
    }

    public function test_it_creates_uri_with_constructor_prefix()
    {
        $path = new StaticPath("/users");
        $handler = new Handler("UsersAction");
        $route = new Route("GET", $path, $handler);

        $this->routes->oneNamed("users.all")->willReturn($route);

        $this->factory->create($path, [])->willReturn("/users");

        $generator = new UriGenerator($this->routes->reveal(), $this->factory->reveal(), "/api");

        $uri = $generator->generate("users.all", []);

        self::assertEquals("/api/users", $uri);
    }

    public function test_method_prefix_overrides_constructor_prefix()
    {
        $path = new StaticPath("/users");
        $handler = new Handler("UsersAction");
        $route = new Route("GET", $path, $handler);

        $this->routes->oneNamed("users.all")->willReturn($route);

        $this->factory->create($path, [])->willReturn("/users");

        $generator = new UriGenerator($this->routes->reveal(), $this->factory->reveal(), "/api");

        $uri = $generator->generate("users.all", [], "/web");

        self::assertEquals("/web/users", $uri);
    }
}
