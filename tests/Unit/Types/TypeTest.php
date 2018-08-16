<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit\Types;

use Svoboda\Router\Types\InvalidTypes;
use Svoboda\Router\Types\Type;
use SvobodaTest\Router\TestCase;

class TypeTest extends TestCase
{
    public function test_it_requires_valid_name_format()
    {
        $this->expectException(InvalidTypes::class);
        $this->expectExceptionMessage(
            "The type name 'number!' is invalid, only alphanumeric characters and underscore are allowed"
        );

        new Type("number!", "\d+");
    }

    public function test_it_requires_valid_pattern()
    {
        $this->expectException(InvalidTypes::class);
        $this->expectExceptionMessage(
            "The pattern '[0-9+' of type 'number' is invalid"
        );

        new Type("number", "[0-9+");
    }

    public function test_it_returns_name()
    {
        $type = new Type("number", "\d+");

        self::assertEquals("number", $type->getName());
    }

    public function test_it_returns_pattern()
    {
        $type = new Type("number", "\d+");

        self::assertEquals("\d+", $type->getPattern());
    }

    public function test_implicit_type_has_empty_name()
    {
        $type = new Type("number", "\d+", true);

        self::assertEquals("", $type->getName());
    }

    public function test_it_creates_implicit_type()
    {
        $type = new Type("any", "[^/]+");

        $implicit = $type->createImplicit();

        self::assertEquals("", $implicit->getName());
        self::assertEquals("any", $type->getName());
    }
}
