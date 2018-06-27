<?php

namespace SvobodaTest\PsrRouter\Route\Path;

use Svoboda\PsrRouter\Route\Path\StaticPath;
use SvobodaTest\PsrRouter\TestCase;

class StaticPathTest extends TestCase
{
    public function test_it_creates_definition_same_as_static_string()
    {
        $path = new StaticPath("/api/users");

        self::assertEquals("/api/users", $path->getDefinition());
    }

    public function test_it_returns_no_attributes()
    {
        $path = new StaticPath("/api/users");

        self::assertEquals([], $path->getAttributes());
    }
}