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
    public function test_build_pattern_for_static_path()
    {
        $static = new StaticPath("/users");

        $pattern = (new PatternBuilder())->buildPattern($static, self::context());

        self::assertEquals("/users", $pattern);
    }

    public function test_build_pattern_for_attribute_path_with_type()
    {
        $attribute = new AttributePath("foo", "num");

        $pattern = (new PatternBuilder())->buildPattern($attribute, self::context());

        self::assertEquals("(?'foo'\d+)", $pattern);
    }

    public function test_build_pattern_for_attribute_path_without_type()
    {
        $attribute = new AttributePath("foo", null);

        $pattern = (new PatternBuilder())->buildPattern($attribute, self::context());

        self::assertEquals("(?'foo'[^/]+)", $pattern);
    }

    public function test_build_pattern_for_optional_path()
    {
        $optional = new OptionalPath(
            new StaticPath("/users")
        );

        $pattern = (new PatternBuilder())->buildPattern($optional, self::context());

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

        $pattern = (new PatternBuilder())->buildPattern($complex, self::context());

        self::assertEquals("/users(?:/(?'id'\d+))?", $pattern);
    }

    /**
     * Returns the testing context.
     *
     * @return Context
     */
    private function context()
    {
        return new Context([
            "any" => "[^/]+",
            "num" => "\d+",
        ], "any");
    }
}
