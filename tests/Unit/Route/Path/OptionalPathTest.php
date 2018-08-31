<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit\Route\Path;

use Svoboda\Router\Route\Attribute;
use Svoboda\Router\Route\Path\AttributePath;
use Svoboda\Router\Route\Path\OptionalPath;
use Svoboda\Router\Route\Path\StaticPath;
use Svoboda\Router\Types\Type;
use SvobodaTest\Router\TestCase;

class OptionalPathTest extends TestCase
{
    /** @var Type */
    private $any;

    protected function setUp()
    {
        $this->any = new Type("any", "[^/]+");
    }

    public function test_square_brackets_surround_the_definition()
    {
        $path = new OptionalPath(
            new StaticPath(
                "/optional"
            )
        );

        $definition = $path->getDefinition();

        self::assertEquals("[/optional]", $definition);
    }

    public function test_nested_attribute_is_made_optional()
    {
        $path = new OptionalPath(
            new AttributePath(
                "foo",
                $this->any
            )
        );

        $attributes = $path->getAttributes();

        self::assertEquals([
            new Attribute("foo", $this->any, false),
        ], $attributes);
    }

    public function test_optional_attribute_remains_optional()
    {
        $path = new OptionalPath(
            new OptionalPath(
                new AttributePath(
                    "foo",
                    $this->any
                )
            )
        );

        $attributes = $path->getAttributes();

        self::assertEquals([
            new Attribute("foo", $this->any, false),
        ], $attributes);
    }
}
