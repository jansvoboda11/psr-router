<?php

declare(strict_types=1);

namespace SvobodaTest\PsrRouter\Parser;

use PHPUnit\Framework\TestCase;
use Svoboda\PsrRouter\Parser\Parser;
use Svoboda\PsrRouter\Route\InvalidRoute;

class ParserTest extends TestCase
{
    public function test_static_path()
    {
        $path = (new Parser())->parse("/users/all");

        self::assertEquals("/users/all", $path->getDefinition());
        self::assertEquals([], $path->getAttributes());
    }

    public function test_path_with_attribute_without_type()
    {
        $path = (new Parser())->parse("/users/{id}");

        self::assertEquals("/users/{id}", $path->getDefinition());
        self::assertEquals([
            ["name" => "id", "type" => null, "required" => true],
        ], $path->getAttributes());
    }

    public function test_path_with_attribute_of_any_type()
    {
        $path = (new Parser())->parse("/users/{id:any}");

        self::assertEquals("/users/{id:any}", $path->getDefinition());
        self::assertEquals([
            ["name" => "id", "type" => "any", "required" => true],
        ], $path->getAttributes());
    }

    public function test_path_with_multiple_attributes()
    {
        $path = (new Parser())->parse("/users/{name}/{id:num}");

        self::assertEquals("/users/{name}/{id:num}", $path->getDefinition());
        self::assertEquals([
            ["name" => "name", "type" => null, "required" => true],
            ["name" => "id", "type" => "num", "required" => true],
        ], $path->getAttributes());
    }

    public function test_path_with_optional_attribute()
    {
        $path = (new Parser())->parse("/users[/{name}]");

        self::assertEquals("/users[/{name}]", $path->getDefinition());
        self::assertEquals([
            ["name" => "name", "type" => null, "required" => false],
        ], $path->getAttributes());
    }

    public function test_path_with_required_and_optional_attributes()
    {
        $path = (new Parser())->parse("/users/{name}[/{id:num}]");

        self::assertEquals("/users/{name}[/{id:num}]", $path->getDefinition());
        self::assertEquals([
            ["name" => "name", "type" => null, "required" => true],
            ["name" => "id", "type" => "num", "required" => false],
        ], $path->getAttributes());
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

        (new Parser())->parse("/users/{}");
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

        (new Parser())->parse("/users/{:any}");
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

        (new Parser())->parse("/users/{wayTooLongAttributeNameNoOneShouldNeed:any}");
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

        (new Parser())->parse("/users/{i%d:any}");
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

        (new Parser())->parse("/users/{id:}");
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

        (new Parser())->parse("/users/{id:a%ny}");
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

        (new Parser())->parse("/users/{id:wayTooLongAttributeTypeNoOneShouldNeed}");
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

        (new Parser())->parse("/users/id}");
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

        (new Parser())->parse("/users/{id");
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

        (new Parser())->parse("/users/{id}]");
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

        (new Parser())->parse("/users[/{id}");
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

        (new Parser())->parse("/users[/{id}]/{name}");
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

        (new Parser())->parse("/users[/{id]}");
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

        (new Parser())->parse("/users/{id[ing}]");
    }
}
