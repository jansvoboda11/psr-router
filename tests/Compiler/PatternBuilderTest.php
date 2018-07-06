<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Compiler;

use Svoboda\Router\Compiler\CompilationFailure;
use Svoboda\Router\Compiler\Context;
use Svoboda\Router\Compiler\PatternBuilder;
use Svoboda\Router\Route\Path\AttributePath;
use Svoboda\Router\Route\Path\OptionalPath;
use Svoboda\Router\Route\Path\StaticPath;
use SvobodaTest\Router\TestCase;

class PatternBuilderTest extends TestCase
{
    /** @var PatternBuilder */
    private $builder;

    protected function setUp()
    {
        $context = new Context([
            "any" => "[^/]+",
            "number" => "\d+",
        ], "any");
        
        $this->builder = new PatternBuilder($context);
    }
    
    public function test_build_pattern_for_static_path()
    {
        $static = new StaticPath("/users");

        $pattern = $this->builder->buildPattern($static);

        self::assertEquals("/users", $pattern);
    }

    public function test_build_pattern_for_attribute_path_with_type()
    {
        $attribute = new AttributePath("foo", "number");

        $pattern = $this->builder->buildPattern($attribute);

        self::assertEquals("(?'foo'\d+)", $pattern);
    }

    public function test_build_pattern_for_attribute_path_without_type()
    {
        $attribute = new AttributePath("foo", null);

        $pattern = $this->builder->buildPattern($attribute);

        self::assertEquals("(?'foo'[^/]+)", $pattern);
    }

    public function test_build_pattern_for_optional_path()
    {
        $optional = new OptionalPath(
            new StaticPath("/users")
        );

        $pattern = $this->builder->buildPattern($optional);

        self::assertEquals("(?:/users)?", $pattern);
    }

    public function test_build_pattern_for_complex_path()
    {
        $complex = new StaticPath(
            "/users",
            new OptionalPath(
                new StaticPath(
                    "/",
                    new AttributePath(
                        "id",
                        "number"
                    )
                )
            )
        );

        $pattern = $this->builder->buildPattern($complex);

        self::assertEquals("/users(?:/(?'id'\d+))?", $pattern);
    }

    public function test_build_pattern_with_unknown_attribute_type()
    {
        $unknown = new AttributePath("id", "unknown");

        $this->expectException(CompilationFailure::class);

        $this->builder->buildPattern($unknown);
    }
}
