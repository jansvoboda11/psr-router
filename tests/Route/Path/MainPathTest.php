<?php

declare(strict_types=1);

namespace SvobodaTest\PsrRouter\Route\Path;

use Mockery;
use PHPUnit\Framework\TestCase;
use Svoboda\PsrRouter\Route\Path\AttributePath;
use Svoboda\PsrRouter\Route\Path\MainPath;
use Svoboda\PsrRouter\Route\Path\RoutePath;
use Svoboda\PsrRouter\Route\Path\StaticPath;

class MainPathTest extends TestCase
{
    public function test_definition_contains_all_parts()
    {
        $static = Mockery::mock(StaticPath::class);
        $static->shouldReceive("getDefinition")->andReturn("/");

        $attribute1 = Mockery::mock(AttributePath::class);
        $attribute1->shouldReceive("getDefinition")->andReturn("{foo:num}");

        $attribute2 = Mockery::mock(AttributePath::class);
        $attribute2->shouldReceive("getDefinition")->andReturn("{bar}");

        $next = Mockery::mock(RoutePath::class);
        $next->shouldReceive("getDefinition")->andReturn("/next");

        $path = new MainPath($static, [$attribute1, $attribute2], $next);

        self::assertEquals("/{foo:num}{bar}/next", $path->getDefinition());
    }

    public function test_it_merges_all_attributes()
    {
        $static = Mockery::mock(StaticPath::class);
        $static->shouldReceive("getAttributes")->andReturn([]);

        $attribute1 = Mockery::mock(AttributePath::class);
        $attribute1->shouldReceive("getAttributes")->andReturn([
            ["name" => "foo", "type" => "any", "required" => true],
        ]);

        $attribute2 = Mockery::mock(AttributePath::class);
        $attribute2->shouldReceive("getAttributes")->andReturn([
            ["name" => "bar", "type" => null, "required" => true],
        ]);

        $next = Mockery::mock(AttributePath::class);
        $next->shouldReceive("getAttributes")->andReturn([
            ["name" => "baz", "type" => "num", "required" => false],
        ]);

        $path = new MainPath($static, [$attribute1, $attribute2], $next);

        self::assertEquals([
            ["name" => "foo", "type" => "any", "required" => true],
            ["name" => "bar", "type" => null, "required" => true],
            ["name" => "baz", "type" => "num", "required" => false],
        ], $path->getAttributes());
    }
}
