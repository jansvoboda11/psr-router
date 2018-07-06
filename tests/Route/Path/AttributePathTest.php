<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Route\Path;

use Svoboda\Router\Route\Attribute;
use Svoboda\Router\Route\Path\AttributePath;
use SvobodaTest\Router\TestCase;

class AttributePathTest extends TestCase
{
    public function test_it_creates_valid_definition_without_type()
    {
        $path = new AttributePath("foo", null);

        $definition = $path->getDefinition();

        self::assertEquals("{foo}", $definition);
    }

    public function test_it_creates_valid_definition_with_type()
    {
        $path = new AttributePath("foo", "any");

        $definition = $path->getDefinition();

        self::assertEquals("{foo:any}", $definition);
    }

    public function test_it_creates_valid_definition_with_next_route_part()
    {
        $path = new AttributePath(
            "foo",
            null,
            new AttributePath(
                "bar",
                null
            )
        );

        $definition = $path->getDefinition();

        self::assertEquals("{foo}{bar}", $definition);
    }

    public function test_it_returns_attribute_without_type()
    {
        $path = new AttributePath("foo", null);

        $attributes = $path->getAttributes();

        self::assertEquals([
            new Attribute("foo", null, true),
        ], $attributes);
    }

    public function test_it_returns_attribute_with_type()
    {
        $path = new AttributePath("foo", "any");

        $attributes = $path->getAttributes();

        self::assertEquals([
            new Attribute("foo", "any", true),
        ], $attributes);
    }

    public function test_it_returns_attribute_of_the_next_route_part()
    {
        $path = new AttributePath(
            "foo",
            "num",
            new AttributePath(
                "bar",
                null
            )
        );

        $attributes = $path->getAttributes();

        self::assertEquals([
            new Attribute("foo", "num", true),
            new Attribute("bar", null, true),
        ], $attributes);
    }
}
