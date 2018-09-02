<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit;

use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Svoboda\Router\Compiler\Compiler;
use Svoboda\Router\Match;
use Svoboda\Router\Matcher\Matcher;
use Svoboda\Router\Route\Path\StaticPath;
use Svoboda\Router\Route\Route;
use Svoboda\Router\RouteCollection;
use Svoboda\Router\Router;
use SvobodaTest\Router\FakeHandler;
use SvobodaTest\Router\TestCase;

class RouterTest extends TestCase
{
    /** @var RouteCollection */
    private $routes;

    /** @var ObjectProphecy|Matcher */
    private $matcher;

    /** @var ObjectProphecy|Compiler */
    private $compiler;

    protected function setUp()
    {
        $this->routes = RouteCollection::create();
        $this->matcher = $this->prophesize(Matcher::class);
        $this->compiler = $this->prophesize(Compiler::class);
    }

    public function test_it_creates_matcher_only_once()
    {
        $request = self::createRequest("GET", "/users");
        $route = new Route("GET", new StaticPath("/users"), new FakeHandler());
        $match = new Match($route, $request);

        $this->compiler->compile($this->routes)->willReturn($this->matcher->reveal());

        $this->matcher->match(Argument::any())->willReturn($match);

        $router = new Router($this->routes, $this->compiler->reveal());

        $router->match($request);
        $router->match($request);

        $this->compiler->compile(Argument::any())->shouldHaveBeenCalledTimes(1);
    }

    public function test_it_uses_created_matcher()
    {
        $request = self::createRequest("GET", "/users");
        $route = new Route("GET", new StaticPath("/users"), new FakeHandler());
        $expectedMatch = new Match($route, $request);

        $this->compiler->compile($this->routes)->willReturn($this->matcher->reveal());

        $this->matcher->match($request)->willReturn($expectedMatch);

        $router = new Router($this->routes, $this->compiler->reveal());

        $match = $router->match($request);

        self::assertEquals($expectedMatch, $match);
    }
}
