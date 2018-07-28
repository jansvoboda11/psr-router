<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit\Route;

use Mockery;
use Mockery\MockInterface;
use Svoboda\Router\Parser\Parser;
use Svoboda\Router\Route\InvalidRoute;
use Svoboda\Router\Route\Path\StaticPath;
use Svoboda\Router\Route\RouteFactory;
use Svoboda\Router\Semantics\Validator;
use Svoboda\Router\Types\Types;
use SvobodaTest\Router\Middleware;
use SvobodaTest\Router\TestCase;

class RouteFactoryTest extends TestCase
{
    /** @var Types */
    private $types;

    /** @var MockInterface|Parser */
    private $parser;

    /** @var MockInterface|Validator */
    private $validator;

    /** @var RouteFactory */
    private $factory;

    protected function setUp()
    {
        $this->types = new Types([
            "any" => "[^/]+",
        ], "any");

        $this->parser = Mockery::mock(Parser::class);
        $this->validator = Mockery::mock(Validator::class);
        $this->factory = new RouteFactory($this->parser, $this->validator);
    }

    public function test_it_parses_and_validates_definition()
    {
        $path = new StaticPath("/path");

        $this->parser
            ->shouldReceive("parse")
            ->with("/path")
            ->andReturn($path)
            ->once();

        $this->validator
            ->shouldReceive("validate")
            ->with($path, $this->types)
            ->once();


        $route = $this->factory->create("GET", "/path", new Middleware("Handler"), $this->types);

        self::assertEquals("GET", $route->getMethod());
        self::assertEquals($path, $route->getPath());
        self::assertEquals(new Middleware("Handler"), $route->getMiddleware());
    }

    public function test_it_fails_when_parser_fails()
    {
        $this->parser
            ->shouldReceive("parse")
            ->with("/path")
            ->andThrow(InvalidRoute::class)
            ->once();

        $this->validator->shouldNotReceive("validate");

        $this->expectException(InvalidRoute::class);

        $this->factory->create("GET", "/path", new Middleware("Handler"), $this->types);
    }

    public function test_it_fails_when_validator_fails()
    {
        $path = new StaticPath("/path");

        $this->parser
            ->shouldReceive("parse")
            ->with("/path")
            ->andReturn($path)
            ->once();

        $this->validator
            ->shouldReceive("validate")
            ->with($path, $this->types)
            ->andThrow(InvalidRoute::class)
            ->once();

        $this->expectException(InvalidRoute::class);

        $this->factory->create("GET", "/path", new Middleware("Handler"), $this->types);
    }
}
