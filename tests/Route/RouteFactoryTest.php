<?php

declare(strict_types=1);

namespace SvobodaTest\PsrRouter\Route;

use Mockery;
use Svoboda\PsrRouter\Parser\Parser;
use Svoboda\PsrRouter\Route\InvalidRoute;
use Svoboda\PsrRouter\Route\Path\EmptyPath;
use Svoboda\PsrRouter\Route\Path\MainPath;
use Svoboda\PsrRouter\Route\Path\StaticPath;
use Svoboda\PsrRouter\Route\RouteFactory;
use Svoboda\PsrRouter\Semantics\Validator;
use SvobodaTest\PsrRouter\TestCase;

class RouteFactoryTest extends TestCase
{
    public function test_it_parses_and_validates_path()
    {
        $path = new MainPath(
            new StaticPath("/path"),
            [],
            new EmptyPath()
        );

        $parser = Mockery::mock(Parser::class);
        $parser->shouldReceive("parse")
            ->with("/path")
            ->andReturn($path)
            ->once();

        $validator = Mockery::mock(Validator::class);
        $validator->shouldReceive("validate")
            ->with($path)
            ->once();

        $factory = new RouteFactory($parser, $validator);

        $route = $factory->createRoute("GET", "/path", "Handler");

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

        $factory->createRoute("GET", "/path", "Handler");
    }

    public function test_it_fails_when_validator_fails()
    {
        $path = new MainPath(
            new StaticPath("/path"),
            [],
            new EmptyPath()
        );

        $parser = Mockery::mock(Parser::class);
        $parser->shouldReceive("parse")
            ->with("/path")
            ->andReturn($path)
            ->once();

        $validator = Mockery::mock(Validator::class);
        $validator->shouldReceive("validate")
            ->with($path)
            ->andThrow(InvalidRoute::class)
            ->once();

        $factory = new RouteFactory($parser, $validator);

        $this->expectException(InvalidRoute::class);

        $factory->createRoute("GET", "/path", "Handler");
    }
}
