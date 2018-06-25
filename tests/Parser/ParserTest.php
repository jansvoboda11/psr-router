<?php

declare(strict_types=1);

namespace SvobodaTest\PsrRouter\Parser;

use PHPUnit\Framework\TestCase;
use Svoboda\PsrRouter\Parser\Parser;
use Svoboda\PsrRouter\InvalidRoute;

class ParserTest extends TestCase
{
    /** @var Parser */
    private $parser;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->parser = new Parser();
    }

    public function test_static_path()
    {
        $ast = $this->parser->parse("/users/all");

        self::assertEquals("/users/all", $ast->getDefinition());
        self::assertEquals([], $ast->getAttributes());
    }

    public function test_path_with_attribute_without_type()
    {
        $ast = $this->parser->parse("/users/{id}");

        self::assertEquals("/users/{id}", $ast->getDefinition());
        self::assertEquals([
            ["name" => "id", "type" => null, "required" => true],
        ], $ast->getAttributes());
    }

    public function test_path_with_attribute_of_all_type()
    {
        $ast = $this->parser->parse("/users/{id:any}");

        self::assertEquals("/users/{id:any}", $ast->getDefinition());
        self::assertEquals([
            ["name" => "id", "type" => "any", "required" => true],
        ], $ast->getAttributes());
    }

    public function test_path_with_multiple_attributes()
    {
        $ast = $this->parser->parse("/users/{name}/{id:num}");

        self::assertEquals("/users/{name}/{id:num}", $ast->getDefinition());
        self::assertEquals([
            ["name" => "name", "type" => null, "required" => true],
            ["name" => "id", "type" => "num", "required" => true],
        ], $ast->getAttributes());
    }

    public function test_path_with_optional_attribute()
    {
        $ast = $this->parser->parse("/users[/{name}]");

        self::assertEquals("/users[/{name}]", $ast->getDefinition());
        self::assertEquals([
            ["name" => "name", "type" => null, "required" => false],
        ], $ast->getAttributes());
    }

    public function test_path_with_required_and_optional_attributes()
    {
        $ast = $this->parser->parse("/users/{name}[/{id:num}]");

        self::assertEquals("/users/{name}[/{id:num}]", $ast->getDefinition());
        self::assertEquals([
            ["name" => "name", "type" => null, "required" => true],
            ["name" => "id", "type" => "num", "required" => false],
        ], $ast->getAttributes());
    }

    public function test_path_with_missing_attribute_info()
    {
        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
The attribute name is missing:
/users/{}
        ^
MESSAGE
        );

        $this->parser->parse("/users/{}");
    }

    public function test_path_with_missing_attribute_name()
    {
        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
The attribute name is missing:
/users/{:any}
        ^
MESSAGE
        );

        $this->parser->parse("/users/{:any}");
    }

    public function test_path_with_too_long_attribute_name()
    {
        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
The attribute name exceeded maximum allowed length of 32 characters:
/users/{wayTooLongAttributeNameNoOneShouldNeed:any}
                                             ^
MESSAGE
        );

        $this->parser->parse("/users/{wayTooLongAttributeNameNoOneShouldNeed:any}");
    }

    public function test_path_with_malformed_attribute_name()
    {
        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
Unexpected character (expected ':', '}', 'alphanumeric'):
/users/{i%d:any}
         ^
MESSAGE
        );

        $this->parser->parse("/users/{i%d:any}");
    }

    public function test_path_with_missing_attribute_type()
    {
        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
The attribute type is missing:
/users/{id:}
           ^
MESSAGE
        );

        $this->parser->parse("/users/{id:}");
    }

    public function test_path_with_malformed_attribute_type()
    {
        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
Unexpected character (expected '}', 'alphanumeric'):
/users/{id:a%ny}
            ^
MESSAGE
        );

        $this->parser->parse("/users/{id:a%ny}");
    }

    public function test_path_with_too_long_attribute_type()
    {
        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
The attribute type exceeded maximum allowed length of 32 characters:
/users/{id:wayTooLongAttributeTypeNoOneShouldNeed}
                                                ^
MESSAGE
        );

        $this->parser->parse("/users/{id:wayTooLongAttributeTypeNoOneShouldNeed}");
    }

    public function test_path_with_missing_left_attribute_brace()
    {
        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
Unexpected character:
/users/id}
         ^
MESSAGE
        );

        $this->parser->parse("/users/id}");
    }

    public function test_path_with_missing_right_attribute_brace()
    {
        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
Unexpected end of route:
/users/{id
          ^
MESSAGE
        );

        $this->parser->parse("/users/{id");
    }

    public function test_path_with_missing_left_optional_bracket()
    {
        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
Unexpected character:
/users/{id}]
           ^
MESSAGE
        );

        $this->parser->parse("/users/{id}]");
    }

    public function test_path_with_missing_right_optional_bracket()
    {
        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
Unexpected end of route:
/users[/{id}
            ^
MESSAGE
        );

        $this->parser->parse("/users[/{id}");
    }

    public function test_path_with_optional_sequence_in_the_middle()
    {
        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
Optional sequence cannot be followed by anything else:
/users[/{id}]/{name}
             ^
MESSAGE
        );

        $this->parser->parse("/users[/{id}]/{name}");
    }

    public function test_path_with_mixed_brackets_one()
    {
        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
Unexpected character (expected ':', '}', 'alphanumeric'):
/users[/{id]}
           ^
MESSAGE
        );

        $this->parser->parse("/users[/{id]}");
    }

    public function test_path_with_mixed_brackets_two()
    {

        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
Unexpected character (expected ':', '}', 'alphanumeric'):
/users/{id[ing}]
          ^
MESSAGE
        );

        $this->parser->parse("/users/{id[ing}]");
    }
}
