<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit\Route;

use Prophecy\Prophecy\ObjectProphecy;
use Svoboda\Router\Parser\Parser;
use Svoboda\Router\Route\InvalidRoute;
use Svoboda\Router\Route\Path\StaticPath;
use Svoboda\Router\Route\Route;
use Svoboda\Router\Route\RouteFactory;
use Svoboda\Router\Types\Types;
use SvobodaTest\Router\Handler;
use SvobodaTest\Router\TestCase;

class RouteFactoryTest extends TestCase
{
    /** @var Types */
    private $types;

    /** @var ObjectProphecy|Parser */
    private $parser;

    /** @var RouteFactory */
    private $factory;

    protected function setUp()
    {
        $this->types = Types::createDefault();

        $this->parser = $this->prophesize(Parser::class);
        $this->factory = new RouteFactory($this->parser->reveal(), $this->types);
    }

    public function test_it_rejects_invalid_http_method()
    {
        $handler = new Handler("Handler");

        $this->expectException(InvalidRoute::class);

        $this->factory->create("INVALID", "/users", $handler, "users", []);
    }

    public function test_it_parses_definition()
    {
        $path = new StaticPath("/users");
        $handler = new Handler("Handler");
        $expectedRoute = new Route("GET", $path, $handler, "users", []);

        $this->parser->parse("/users", $this->types)->willReturn($path);

        $route = $this->factory->create("GET", "/users", $handler, "users", []);

        self::assertEquals($expectedRoute, $route);
    }

    public function test_it_fails_when_parser_fails()
    {
        $handler = new Handler("Handler");

        $this->parser->parse("/users/{id", $this->types)->willThrow(InvalidRoute::class);

        $this->expectException(InvalidRoute::class);

        $this->factory->create("GET", "/users/{id", $handler, "users", []);
    }
}
