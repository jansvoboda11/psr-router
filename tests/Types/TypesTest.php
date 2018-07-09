<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Types;

use Svoboda\Router\Types\InvalidTypes;
use Svoboda\Router\Types\Types;
use SvobodaTest\Router\TestCase;

class TypesTest extends TestCase
{
    public function test_it_requires_at_least_one_type_pattern()
    {
        $this->expectException(InvalidTypes::class);
        $this->expectExceptionMessage(<<<MESSAGE
At least one type pattern must be provided
MESSAGE
    );

        new Types([], "");
    }

    public function test_it_requires_existing_implicit_type()
    {
        $this->expectException(InvalidTypes::class);
        $this->expectExceptionMessage(<<<MESSAGE
The implicit attribute type 'any' has no pattern
MESSAGE
        );

        new Types([
            "num" => "\d+",
        ], "any");
    }

    public function test_it_requires_valid_type_name_format()
    {
        $this->expectException(InvalidTypes::class);
        $this->expectExceptionMessage(<<<MESSAGE
The type name 'num!' is invalid, only alphanumeric characters and underscore are allowed
MESSAGE
        );

        new Types([
            "any" => "[^/]+",
            "num!" => "\d+",
        ], "any");
    }

    public function test_it_requires_valid_type_pattern()
    {
        $this->expectException(InvalidTypes::class);
        $this->expectExceptionMessage(<<<MESSAGE
The pattern '[0-9+' of attribute 'num' is invalid
MESSAGE
        );

        new Types([
            "any" => "[^/]+",
            "num" => "[0-9+",
        ], "any");
    }
}
