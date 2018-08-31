<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit\Route\Path;

use Svoboda\Router\Route\Attribute;
use Svoboda\Router\Route\Path\AttributePath;
use Svoboda\Router\Types\Type;
use SvobodaTest\Router\TestCase;

class AttributePathTest extends TestCase
{
    /** @var Type */
    private $implicit;

    /** @var Type */
    private $any;

    /** @var Type */
    private $number;

    protected function setUp()
    {
        $this->implicit = new Type("any", "[^/]+", true);
        $this->any = new Type("any", "[^/]+");
        $this->number = new Type("number", "\d+");
    }

    public function test_type_pattern_is_returned()
    {
        $path = new AttributePath("foo", $this->number);

        self::assertEquals("\d+", $path->getTypePattern());
    }

    public function test_definition_is_created()
    {
        $path = new AttributePath("foo", $this->any);

        $definition = $path->getDefinition();

        self::assertEquals("{foo:any}", $definition);
    }

    public function test_definition_contains_that_of_nested_part()
    {
        $path = new AttributePath(
            "foo",
            $this->implicit,
            new AttributePath(
                "bar",
                $this->implicit
            )
        );

        $definition = $path->getDefinition();

        self::assertEquals("{foo}{bar}", $definition);
    }

    public function test_attribute_is_returned()
    {
        $path = new AttributePath("foo", $this->any);

        $attributes = $path->getAttributes();

        self::assertEquals([
            new Attribute("foo", $this->any, true),
        ], $attributes);
    }

    public function test_attributes_include_that_of_nested_part()
    {
        $path = new AttributePath(
            "foo",
            $this->number,
            new AttributePath(
                "bar",
                $this->any
            )
        );

        $attributes = $path->getAttributes();

        self::assertEquals([
            new Attribute("foo", $this->number, true),
            new Attribute("bar", $this->any, true),
        ], $attributes);
    }
}
