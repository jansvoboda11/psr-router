<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit\Compiler\Pattern;

use Svoboda\Router\Compiler\Pattern\PathPattern;
use Svoboda\Router\Route\Path\AttributePath;
use Svoboda\Router\Route\Path\OptionalPath;
use Svoboda\Router\Route\Path\StaticPath;
use Svoboda\Router\Types\Type;
use SvobodaTest\Router\TestCase;

class PathPatternTest extends TestCase
{
    /** @var Type */
    private $number;

    protected function setUp()
    {
        $this->number = new Type("number", "\d+");
    }

    public function test_build_pattern_for_static_path()
    {
        $static = new StaticPath("/users");

        $pattern = new PathPattern($static);

        self::assertEquals("/users", $pattern);
    }

    public function test_build_pattern_for_attribute_path_with_type()
    {
        $attribute = new AttributePath("foo", $this->number);

        $pattern = new PathPattern($attribute);

        self::assertEquals("(\d+)", $pattern);
    }

    public function test_build_pattern_for_optional_path()
    {
        $optional = new OptionalPath(
            new StaticPath("/users")
        );

        $pattern = new PathPattern($optional);

        self::assertEquals("(?:/users)?", $pattern);
    }

    public function test_build_pattern_for_complex_path()
    {
        $complex = new StaticPath("/users",
            new OptionalPath(
                new StaticPath("/",
                    new AttributePath("id", $this->number)
                )
            )
        );

        $pattern = new PathPattern($complex);

        self::assertEquals("/users(?:/(\d+))?", $pattern);
    }
}
