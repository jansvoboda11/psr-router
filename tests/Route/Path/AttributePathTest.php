<?php

declare(strict_types=1);

namespace SvobodaTest\PsrRouter\Route\Path;

use PHPUnit\Framework\TestCase;
use Svoboda\PsrRouter\Route\Path\AttributePath;

class AttributePathTest extends TestCase
{
    public function test_it_creates_valid_definition_without_type()
    {
        $path = new AttributePath("foo");

        self::assertEquals("{foo}", $path->getDefinition());
    }

    public function test_it_creates_valid_definition_with_type()
    {
        $path = new AttributePath("foo", "any");

        self::assertEquals("{foo:any}", $path->getDefinition());
    }

    public function test_it_returns_attribute_without_type()
    {
        $path = new AttributePath("foo");

        self::assertEquals([
            ["name" => "foo", "type" => null, "required" => true],
        ], $path->getAttributes());
    }

    public function test_it_returns_attribute_with_type()
    {
        $path = new AttributePath("foo", "any");

        self::assertEquals([
            ["name" => "foo", "type" => "any", "required" => true],
        ], $path->getAttributes());
    }
}
