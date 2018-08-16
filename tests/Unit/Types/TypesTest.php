<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit\Types;

use Svoboda\Router\Types\InvalidTypes;
use Svoboda\Router\Types\Type;
use Svoboda\Router\Types\TypeCollection;
use SvobodaTest\Router\TestCase;

class TypesTest extends TestCase
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

    public function test_it_requires_at_least_one_type()
    {
        $this->expectException(InvalidTypes::class);
        $this->expectExceptionMessage(
            "At least one type must be provided"
        );

        new TypeCollection([]);
    }

    public function test_it_returns_implicit_type()
    {

        $types = new TypeCollection([
            $this->any,
            $this->number,
        ]);

        self::assertEquals($this->implicit, $types->getNamed(""));
    }

    public function test_it_contains_registered_type()
    {
        $types = new TypeCollection([
            $this->any,
            $this->number,
        ]);

        self::assertTrue($types->hasNamed("number"));
    }

    public function test_it_does_not_contain_not_registered_type()
    {
        $types = new TypeCollection([
            $this->any,
            $this->number,
        ]);

        self::assertFalse($types->hasNamed("empty"));
    }

    public function test_it_returns_pattern_of_registered_type()
    {
        $types = new TypeCollection([
            $this->any,
            $this->number,
        ]);

        self::assertEquals("\d+", $types->getPatternFor("number"));
    }
}
