<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit;

use Mockery;
use Mockery\MockInterface;
use Svoboda\Router\Compiler\Compiler;
use Svoboda\Router\Compiler\Matcher;
use Svoboda\Router\Match;
use Svoboda\Router\Route\Path\StaticPath;
use Svoboda\Router\Route\Route;
use Svoboda\Router\RouteCollection;
use Svoboda\Router\Router;
use SvobodaTest\Router\Handler;
use SvobodaTest\Router\TestCase;

class RouterTest extends TestCase
{
    /** @var MockInterface|RouteCollection */
    private $routes;

    /** @var MockInterface|Matcher */
    private $matcher;

    /** @var MockInterface|Compiler */
    private $compiler;

    protected function setUp()
    {
        $this->routes = Mockery::mock(RouteCollection::class);
        $this->matcher = Mockery::mock(Matcher::class);
        $this->compiler = Mockery::mock(Compiler::class);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function test_it_creates_matcher_only_once()
    {
        $request = self::createRequest("GET", "/users");

        $this->compiler
            ->shouldReceive("compile")
            ->with($this->routes)
            ->andReturn($this->matcher)
            ->once();

        $this->matcher
            ->shouldReceive("match")
            ->with($request)
            ->twice();

        $router = new Router($this->routes, $this->compiler);

        $router->match($request);
        $router->match($request);
    }

    public function test_it_uses_created_matcher()
    {
        $request = self::createRequest("GET", "/users");

        $route = new Route("GET", new StaticPath("/users"), new Handler("Users"));
        $expectedMatch = new Match($route, $request);

        $this->compiler
            ->shouldReceive("compile")
            ->with($this->routes)
            ->andReturn($this->matcher)
            ->once();

        $this->matcher
            ->shouldReceive("match")
            ->with($request)
            ->andReturn($expectedMatch)
            ->once();

        $router = new Router($this->routes, $this->compiler);

        $match = $router->match($request);

        self::assertEquals($expectedMatch, $match);
    }
}
