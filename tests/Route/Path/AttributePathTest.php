<?php

declare(strict_types=1);

namespace SvobodaTest\PsrRouter\Route\Path;

use Svoboda\PsrRouter\Route\Path\AttributePath;
use SvobodaTest\PsrRouter\TestCase;

class AttributePathTest extends TestCase
{
    public function test_it_creates_valid_definition_without_type()
    {
        $path = new AttributePath("foo");

        $definition = $path->getDefinition();

        self::assertEquals("{foo}", $definition);
    }

    public function test_it_creates_valid_definition_with_type()
    {
        $path = new AttributePath("foo", "any");

        $definition = $path->getDefinition();

        self::assertEquals("{foo:any}", $definition);
    }

    public function test_it_returns_attribute_without_type()
    {
        $path = new AttributePath("foo");

        $attributes = $path->getAttributes();

        self::assertEquals([
            ["name" => "foo", "type" => null, "required" => true],
        ], $attributes);
    }

    public function test_it_returns_attribute_with_type()
    {
        $path = new AttributePath("foo", "any");

        $attributes = $path->getAttributes();

        self::assertEquals([
            ["name" => "foo", "type" => "any", "required" => true],
        ], $attributes);
    }
}
