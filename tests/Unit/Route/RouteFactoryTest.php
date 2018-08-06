<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit\Route;

use Mockery;
use Mockery\MockInterface;
use Svoboda\Router\Parser\Parser;
use Svoboda\Router\Route\InvalidRoute;
use Svoboda\Router\Route\Path\StaticPath;
use Svoboda\Router\Route\RouteFactory;
use Svoboda\Router\Types\Types;
use SvobodaTest\Router\Handler;
use SvobodaTest\Router\TestCase;

class RouteFactoryTest extends TestCase
{
    /** @var Types */
    private $types;

    /** @var MockInterface|Parser */
    private $parser;

    /** @var RouteFactory */
    private $factory;

    protected function setUp()
    {
        $this->types = new Types([
            "any" => "[^/]+",
        ], "any");

        $this->parser = Mockery::mock(Parser::class);
        $this->factory = new RouteFactory($this->parser, $this->types);
    }

    public function test_it_rejects_invalid_http_method()
    {
        $handler = new Handler("Handler");

        $this->expectException(InvalidRoute::class);

        $this->factory->create("INVALID", "/path", $handler, []);
    }

    public function test_it_parses_definition()
    {
        $path = new StaticPath("/path");
        $handler = new Handler("Handler");

        $this->parser
            ->shouldReceive("parse")
            ->with("/path", $this->types)
            ->andReturn($path)
            ->once();

        $route = $this->factory->create("GET", "/path", $handler, []);

        self::assertEquals("GET", $route->getMethod());
        self::assertEquals($path, $route->getPath());
        self::assertEquals($handler, $route->getHandler());
    }

    public function test_it_fails_when_parser_fails()
    {
        $handler = new Handler("Handler");

        $this->parser
            ->shouldReceive("parse")
            ->with("/path", $this->types)
            ->andThrow(InvalidRoute::class)
            ->once();

        $this->expectException(InvalidRoute::class);

        $this->factory->create("GET", "/path", $handler, []);
    }
}
