<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit\Types;

use Svoboda\Router\Types\InvalidTypes;
use Svoboda\Router\Types\Types;
use SvobodaTest\Router\TestCase;

class TypesTest extends TestCase
{
    public function test_it_requires_at_least_one_type_pattern()
    {
        $this->expectException(InvalidTypes::class);
        $this->expectExceptionMessage(
            "At least one type pattern must be provided"
        );

        new Types([], "");
    }

    public function test_it_requires_existing_implicit_type()
    {
        $this->expectException(InvalidTypes::class);
        $this->expectExceptionMessage(
            "The implicit attribute type 'any' has no pattern"
        );

        new Types([
            "num" => "\d+",
        ], "any");
    }

    public function test_it_requires_valid_type_name_format()
    {
        $this->expectException(InvalidTypes::class);
        $this->expectExceptionMessage(
            "The type name 'num!' is invalid, only alphanumeric characters and underscore are allowed"
        );

        new Types([
            "any" => "[^/]+",
            "num!" => "\d+",
        ], "any");
    }

    public function test_it_requires_valid_type_pattern()
    {
        $this->expectException(InvalidTypes::class);
        $this->expectExceptionMessage(
            "The pattern '[0-9+' of attribute 'num' is invalid"
        );

        new Types([
            "any" => "[^/]+",
            "num" => "[0-9+",
        ], "any");
    }

    public function test_it_returns_patterns()
    {
        $types = new Types([
            "any" => "[^/]+",
            "num" => "[0-9]+",
        ], "any");

        $patterns = $types->getPatterns();

        self::assertEquals([
            "any" => "[^/]+",
            "num" => "[0-9]+",
        ], $patterns);
    }

    public function test_it_returns_implicit_pattern()
    {
        $types = new Types([
            "any" => "[^/]+",
            "num" => "[0-9]+",
        ], "any");

        self::assertEquals("any", $types->getImplicit());
    }
}
