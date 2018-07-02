<?php

namespace SvobodaTest\PsrRouter\Route\Path;

use Svoboda\PsrRouter\Route\Attribute;
use Svoboda\PsrRouter\Route\Path\AttributePath;
use Svoboda\PsrRouter\Route\Path\StaticPath;
use SvobodaTest\PsrRouter\TestCase;

class StaticPathTest extends TestCase
{
    public function test_it_creates_definition_same_as_static_string()
    {
        $path = new StaticPath("/api/users");

        $definition = $path->getDefinition();

        self::assertEquals("/api/users", $definition);
    }

    public function test_it_creates_definition_next_route_part()
    {
        $path = new StaticPath(
            "/api/users/",
            new AttributePath(
                "foo",
                "num"
            )
        );

        $definition = $path->getDefinition();

        self::assertEquals("/api/users/{foo:num}", $definition);
    }

    public function test_it_returns_no_attributes()
    {
        $path = new StaticPath("/api/users");

        $attributes = $path->getAttributes();

        self::assertEquals([], $attributes);
    }

    public function test_it_returns_attributes_of_next_route_part()
    {
        $path = new StaticPath(
            "/api/users/",
            new AttributePath(
                "foo",
                "num"
            )
        );

        $attributes = $path->getAttributes();

        self::assertEquals([
            new Attribute("foo", "num", true),
        ], $attributes);
    }
}
