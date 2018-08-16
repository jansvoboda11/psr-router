<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit\Route;

use Svoboda\Router\Route\Attribute;
use Svoboda\Router\Types\Type;
use SvobodaTest\Router\TestCase;

class AttributeTest extends TestCase
{
    /** @var Type */
    private $number;

    protected function setUp()
    {
        parent::setUp();

        $this->number = new Type("number", "\d+");
    }

    public function test_it_returns_name()
    {
        $attribute = new Attribute("id", $this->number, false);

        self::assertEquals("id", $attribute->getName());
    }

    public function test_it_returns_type()
    {
        $attribute = new Attribute("id", $this->number, false);

        self::assertEquals($this->number, $attribute->getType());
    }

    public function test_it_reports_as_required()
    {
        $attribute = new Attribute("id", $this->number, false);

        self::assertFalse($attribute->isRequired());
    }

    public function test_it_makes_optional_clone()
    {
        $required = new Attribute("id", $this->number, true);

        $optional = $required->createOptional();

        self::assertFalse($optional->isRequired());
        self::assertTrue($required->isRequired());
    }
}
