<?php

declare(strict_types=1);

namespace SvobodaTest\PsrRouter\Route\Path;

use Svoboda\PsrRouter\Route\Path\AttributePath;
use Svoboda\PsrRouter\Route\Path\EmptyPath;
use Svoboda\PsrRouter\Route\Path\MainPath;
use Svoboda\PsrRouter\Route\Path\OptionalPath;
use Svoboda\PsrRouter\Route\Path\StaticPath;
use SvobodaTest\PsrRouter\TestCase;

class MainPathTest extends TestCase
{
    public function test_definition_contains_all_parts()
    {
        $path = new MainPath(
            new StaticPath("/"),
            [
                new AttributePath("foo", "num"),
                new AttributePath("bar", null)
            ],
            new MainPath(
                new StaticPath("/next"),
                [],
                new EmptyPath()
            )
        );

        $definition = $path->getDefinition();

        self::assertEquals("/{foo:num}{bar}/next", $definition);
    }

    public function test_it_merges_all_attributes()
    {
        $path = new MainPath(
            new StaticPath(""),
            [
                new AttributePath("foo", "any"),
                new AttributePath("bar"),
            ],
            new OptionalPath(
                new MainPath(
                    new StaticPath(""),
                    [
                        new AttributePath("baz", "num"),
                    ],
                    new EmptyPath()
                )
            )
        );

        $attributes = $path->getAttributes();

        self::assertEquals([
            ["name" => "foo", "type" => "any", "required" => true],
            ["name" => "bar", "type" => null, "required" => true],
            ["name" => "baz", "type" => "num", "required" => false],
        ], $attributes);
    }
}
