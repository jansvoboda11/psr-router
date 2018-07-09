<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Route;

use Mockery;
use Svoboda\Router\Parser\Parser;
use Svoboda\Router\Route\InvalidRoute;
use Svoboda\Router\Route\Path\StaticPath;
use Svoboda\Router\Route\RouteFactory;
use Svoboda\Router\Semantics\Validator;
use Svoboda\Router\Types\Types;
use SvobodaTest\Router\TestCase;

class RouteFactoryTest extends TestCase
{
    /** @var Types */
    private $types;

    public function setUp()
    {
        $this->types = new Types([
            "any" => "[^/]+",
        ], "any");
    }

    public function test_it_parses_and_validates_definition()
    {
        $path = new StaticPath("/path");

        $parser = Mockery::mock(Parser::class);
        $parser->shouldReceive("parse")
            ->with("/path")
            ->andReturn($path)
            ->once();

        $validator = Mockery::mock(Validator::class);
        $validator->shouldReceive("validate")
            ->with($path, $this->types)
            ->once();

        $factory = new RouteFactory($parser, $validator);

        $route = $factory->create("GET", "/path", "Handler", $this->types);

        self::assertEquals("GET", $route->getMethod());
        self::assertEquals($path, $route->getPath());
        self::assertEquals("Handler", $route->getHandler());
    }

    public function test_it_fails_when_parser_fails()
    {
        $parser = Mockery::mock(Parser::class);
        $parser->shouldReceive("parse")
            ->with("/path")
            ->andThrow(InvalidRoute::class)
            ->once();

        $validator = Mockery::mock(Validator::class);
        $validator->shouldNotReceive("validate");

        $factory = new RouteFactory($parser, $validator);

        $this->expectException(InvalidRoute::class);

        $factory->create("GET", "/path", "Handler", $this->types);
    }

    public function test_it_fails_when_validator_fails()
    {
        $path = new StaticPath("/path");

        $parser = Mockery::mock(Parser::class);
        $parser->shouldReceive("parse")
            ->with("/path")
            ->andReturn($path)
            ->once();

        $validator = Mockery::mock(Validator::class);
        $validator->shouldReceive("validate")
            ->with($path, $this->types)
            ->andThrow(InvalidRoute::class)
            ->once();

        $factory = new RouteFactory($parser, $validator);

        $this->expectException(InvalidRoute::class);

        $factory->create("GET", "/path", "Handler", $this->types);
    }
}
