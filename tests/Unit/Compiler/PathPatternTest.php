<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit\Compiler;

use Svoboda\Router\Compiler\PathPattern;
use Svoboda\Router\Route\Path\AttributePath;
use Svoboda\Router\Route\Path\OptionalPath;
use Svoboda\Router\Route\Path\StaticPath;
use Svoboda\Router\Types\Types;
use SvobodaTest\Router\TestCase;

class PathPatternTest extends TestCase
{
    /** @var Types */
    private $types;

    protected function setUp()
    {
        $this->types = new Types([
            "any" => "[^/]+",
            "number" => "\d+",
        ], "any");
    }

    public function test_build_pattern_for_static_path()
    {
        $static = new StaticPath("/users");

        $pattern = new PathPattern($static, $this->types);

        self::assertEquals("/users", $pattern);
    }

    public function test_build_pattern_for_attribute_path_with_type()
    {
        $attribute = new AttributePath("foo", "number");

        $pattern = new PathPattern($attribute, $this->types);

        self::assertEquals("(?'foo'\d+)", $pattern);
    }

    public function test_build_pattern_for_attribute_path_without_type()
    {
        $attribute = new AttributePath("foo", null);

        $pattern = new PathPattern($attribute, $this->types);

        self::assertEquals("(?'foo'[^/]+)", $pattern);
    }

    public function test_build_pattern_for_optional_path()
    {
        $optional = new OptionalPath(
            new StaticPath("/users")
        );

        $pattern = new PathPattern($optional, $this->types);

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

        $pattern = new PathPattern($complex, $this->types);

        self::assertEquals("/users(?:/(?'id'\d+))?", $pattern);
    }
}
