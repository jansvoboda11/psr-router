<?php

declare(strict_types=1);

namespace SvobodaTest\PsrRouter\Route\Path;

use Svoboda\PsrRouter\Route\Attribute;
use Svoboda\PsrRouter\Route\Path\AttributePath;
use Svoboda\PsrRouter\Route\Path\OptionalPath;
use Svoboda\PsrRouter\Route\Path\StaticPath;
use SvobodaTest\PsrRouter\TestCase;

class OptionalPathTest extends TestCase
{
    public function test_it_uses_brackets_in_definition()
    {
        $path = new OptionalPath(
            new StaticPath(
                "/optional"
            )
        );

        $definition = $path->getDefinition();

        self::assertEquals("[/optional]", $definition);
    }

    public function test_it_makes_attributes_optional()
    {
        $path = new OptionalPath(
            new AttributePath(
                "foo",
                "any"
            )
        );

        $attributes = $path->getAttributes();

        self::assertEquals([
            new Attribute("foo", "any", false),
        ], $attributes);
    }

    public function test_it_keeps_optional_attributes_optional()
    {
        $path = new OptionalPath(
            new OptionalPath(
                new AttributePath(
                    "foo",
                    "any"
                )
            )
        );

        $attributes = $path->getAttributes();

        self::assertEquals([
            new Attribute("foo", "any", false),
        ], $attributes);
    }
}
