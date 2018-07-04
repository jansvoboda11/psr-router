<?php

declare(strict_types=1);

namespace SvobodaTest\PsrRouter\Compiler;

use Svoboda\PsrRouter\Compiler\Context;
use Svoboda\PsrRouter\Compiler\PatternBuilder;
use Svoboda\PsrRouter\Route\Path\AttributePath;
use Svoboda\PsrRouter\Route\Path\OptionalPath;
use Svoboda\PsrRouter\Route\Path\StaticPath;
use SvobodaTest\PsrRouter\TestCase;

class PatternBuilderTest extends TestCase
{
    /** @var PatternBuilder */
    private $builder;

    protected function setUp()
    {
        $context = new Context([
            "any" => "[^/]+",
            "num" => "\d+",
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
        $attribute = new AttributePath("foo", "num");

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
                        "num"
                    )
                )
            )
        );

        $pattern = $this->builder->buildPattern($complex);

        self::assertEquals("/users(?:/(?'id'\d+))?", $pattern);
    }
}
