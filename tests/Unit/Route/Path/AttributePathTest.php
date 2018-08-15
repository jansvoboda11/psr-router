<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit\Route\Path;

use Svoboda\Router\Route\Attribute;
use Svoboda\Router\Route\Path\AttributePath;
use Svoboda\Router\Types\TypeCollection;
use SvobodaTest\Router\TestCase;

class AttributePathTest extends TestCase
{
    /** @var TypeCollection */
    private $types;

    protected function setUp()
    {
        parent::setUp();

        $this->types = TypeCollection::createDefault();
    }

    public function test_it_returns_default_pattern_without_type()
    {
        $path = new AttributePath("foo", null, $this->types);

        self::assertEquals("[^/]+", $path->getPattern());
    }

    public function test_it_returns_correct_pattern_with_type()
    {
        $path = new AttributePath("foo", "number", $this->types);

        self::assertEquals("\d+", $path->getPattern());
    }

    public function test_it_creates_valid_definition_without_type()
    {
        $path = new AttributePath("foo", null, $this->types);

        $definition = $path->getDefinition();

        self::assertEquals("{foo}", $definition);
    }

    public function test_it_creates_valid_definition_with_type()
    {
        $path = new AttributePath("foo", "any", $this->types);

        $definition = $path->getDefinition();

        self::assertEquals("{foo:any}", $definition);
    }

    public function test_it_creates_valid_definition_with_next_route_part()
    {
        $path = new AttributePath(
            "foo",
            null,
            $this->types,
            new AttributePath(
                "bar",
                null,
                $this->types
            )
        );

        $definition = $path->getDefinition();

        self::assertEquals("{foo}{bar}", $definition);
    }

    public function test_it_returns_attribute_with_implicit_type()
    {
        $path = new AttributePath("foo", null, $this->types);

        $attributes = $path->getAttributes();

        self::assertEquals([
            new Attribute("foo", "any", true),
        ], $attributes);
    }

    public function test_it_returns_attribute_with_type()
    {
        $path = new AttributePath("foo", "any", $this->types);

        $attributes = $path->getAttributes();

        self::assertEquals([
            new Attribute("foo", "any", true),
        ], $attributes);
    }

    public function test_it_returns_attribute_of_the_next_route_part()
    {
        $path = new AttributePath(
            "foo",
            "number",
            $this->types,
            new AttributePath(
                "bar",
                "any",
                $this->types
            )
        );

        $attributes = $path->getAttributes();

        self::assertEquals([
            new Attribute("foo", "number", true),
            new Attribute("bar", 'any', true),
        ], $attributes);
    }
}
