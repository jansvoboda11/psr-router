<?php

declare(strict_types=1);

namespace SvobodaTest\PsrRouter\Route\Path;

use Mockery;
use PHPUnit\Framework\TestCase;
use Svoboda\PsrRouter\Route\Path\OptionalPath;
use Svoboda\PsrRouter\Route\Path\RoutePath;

class OptionalPathTest extends TestCase
{
    public function test_it_uses_brackets_in_definition()
    {
        $optional = Mockery::mock(RoutePath::class);
        $optional->shouldReceive("getDefinition")->andReturn("/optional");

        $path = new OptionalPath($optional);

        self::assertEquals("[/optional]", $path->getDefinition());
    }

    public function test_it_makes_attributes_optional()
    {
        $optional = Mockery::mock(RoutePath::class);
        $optional->shouldReceive("getAttributes")->andReturn([
            ["name" => "foo", "type" => "any", "required" => true],
        ]);

        $path = new OptionalPath($optional);

        self::assertEquals([
            ["name" => "foo", "type" => "any", "required" => false],
        ], $path->getAttributes());
    }

    public function test_it_keeps_optional_attributes_optional()
    {
        $optional = Mockery::mock(RoutePath::class);
        $optional->shouldReceive("getAttributes")->andReturn([
            ["name" => "foo", "type" => "any", "required" => false],
        ]);

        $path = new OptionalPath($optional);

        self::assertEquals([
            ["name" => "foo", "type" => "any", "required" => false],
        ], $path->getAttributes());
    }
}
