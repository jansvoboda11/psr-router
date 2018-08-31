<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit\Route\Path;

use Svoboda\Router\Route\Attribute;
use Svoboda\Router\Route\Path\AttributePath;
use Svoboda\Router\Route\Path\StaticPath;
use Svoboda\Router\Types\Type;
use SvobodaTest\Router\TestCase;

class StaticPathTest extends TestCase
{
    /** @var Type */
    private $number;

    protected function setUp()
    {
        $this->number = new Type("number", "\d+");
    }

    public function test_definition_is_created()
    {
        $path = new StaticPath("/api/users");

        $definition = $path->getDefinition();

        self::assertEquals("/api/users", $definition);
    }

    public function test_definition_contains_that_of_nested_part()
    {
        $path = new StaticPath(
            "/api/users/",
            new AttributePath(
                "foo",
                $this->number
            )
        );

        $definition = $path->getDefinition();

        self::assertEquals("/api/users/{foo:number}", $definition);
    }

    public function test_no_attributes_are_returned()
    {
        $path = new StaticPath("/api/users");

        $attributes = $path->getAttributes();

        self::assertEquals([], $attributes);
    }

    public function test_attributes_of_the_nested_part_are_returned()
    {
        $path = new StaticPath(
            "/api/users/",
            new AttributePath(
                "foo",
                $this->number
            )
        );

        $attributes = $path->getAttributes();

        self::assertEquals([
            new Attribute("foo", $this->number, true),
        ], $attributes);
    }
}
