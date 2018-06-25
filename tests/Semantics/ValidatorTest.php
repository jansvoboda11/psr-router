<?php

declare(strict_types=1);

namespace SvobodaTest\PsrRouter\Semantics;

use PHPStan\Testing\TestCase;
use Svoboda\PsrRouter\InvalidRoute;
use Svoboda\PsrRouter\Parser\Parser;
use Svoboda\PsrRouter\Semantics\Validator;

class ValidatorTest extends TestCase
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->parser = new Parser();
        $this->validator = new Validator();
    }

    public function test_path_without_attributes()
    {
        $ast = $this->parser->parse("/users");

        $this->validator->validate($ast);

        self::assertTrue(true);
    }

    public function test_path_with_one_required_attribute()
    {

        $ast = $this->parser->parse("/users/{name}");

        $this->validator->validate($ast);

        self::assertTrue(true);
    }

    public function test_path_with_one_optional_attribute()
    {

        $ast = $this->parser->parse("/users[/{name}]");

        $this->validator->validate($ast);

        self::assertTrue(true);
    }

    public function test_path_with_two_attributes()
    {

        $ast = $this->parser->parse("/users/{id}/{name}");

        $this->validator->validate($ast);

        self::assertTrue(true);
    }

    public function test_path_with_required_and_optional_attribute()
    {

        $ast = $this->parser->parse("/users/{id}[/{name}]");

        $this->validator->validate($ast);

        self::assertTrue(true);
    }

    public function test_path_with_two_required_attributes_of_same_name()
    {
        $ast = $this->parser->parse("/users/{id}/{id}");

        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
Multiple attributes with name 'id':
/users/{id}/{id}
MESSAGE
        );

        $this->validator->validate($ast);
    }

    public function test_path_with_required_and_optional_attribute_of_same_name()
    {
        $ast = $this->parser->parse("/users/{id}[/{id}]");

        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
Multiple attributes with name 'id':
/users/{id}[/{id}]
MESSAGE
        );

        $this->validator->validate($ast);
    }
}
