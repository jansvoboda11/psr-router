<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit\Parser;

use Svoboda\Router\Parser\Parser;
use Svoboda\Router\Route\InvalidRoute;
use Svoboda\Router\Route\Path\AttributePath;
use Svoboda\Router\Route\Path\OptionalPath;
use Svoboda\Router\Route\Path\StaticPath;
use Svoboda\Router\Types\TypeCollection;
use SvobodaTest\Router\TestCase;

class ParserTest extends TestCase
{
    /** @var Parser */
    private $parser;

    /** @var TypeCollection */
    private $types;

    protected function setUp()
    {
        $this->parser = new Parser();

        $this->types = TypeCollection::createDefault();
    }

    public function test_parse_static_path()
    {
        $definition = "/users/all";

        $path = $this->parser->parse($definition, $this->types);

        $expectedPath = new StaticPath("/users/all");

        self::assertEquals($expectedPath, $path);
    }

    public function test_parse_path_with_attribute_without_type()
    {
        $definition = "/users/{id}";

        $path = $this->parser->parse($definition, $this->types);

        $expectedPath = new StaticPath(
            "/users/",
            new AttributePath(
                "id",
                null,
                $this->types
            )
        );

        self::assertEquals($expectedPath, $path);
    }

    public function test_parse_path_with_attribute_of_any_type()
    {
        $definition = "/users/{id:any}";

        $path = $this->parser->parse($definition, $this->types);

        $expectedPath = new StaticPath(
            "/users/",
            new AttributePath(
                "id",
                "any",
                $this->types
            )
        );

        self::assertEquals($expectedPath, $path);
    }

    public function test_parse_path_with_multiple_attributes()
    {
        $definition = "/users/{name}/{id:number}";

        $path = $this->parser->parse($definition, $this->types);

        $expectedPath = new StaticPath(
            "/users/",
            new AttributePath(
                "name",
                null,
                $this->types,
                new StaticPath(
                    "/",
                    new AttributePath(
                        "id",
                        "number",
                        $this->types
                    )
                )
            )
        );

        self::assertEquals($expectedPath, $path);
    }

    public function test_parse_path_with_optional_attribute()
    {
        $definition = "/users[/{name}]";

        $path = $this->parser->parse($definition, $this->types);

        $expectedPath = new StaticPath(
            "/users",
            new OptionalPath(
                new StaticPath(
                    "/",
                    new AttributePath(
                        "name",
                        null,
                        $this->types
                    )
                )
            )
        );

        self::assertEquals($expectedPath, $path);
    }

    public function test_parse_path_with_required_and_optional_attributes()
    {
        $definition = "/users/{name}[/{id:number}]";

        $path = $this->parser->parse($definition, $this->types);

        $expectedPath = new StaticPath(
            "/users/",
            new AttributePath(
                "name",
                null,
                $this->types,
                new OptionalPath(
                    new StaticPath(
                        "/",
                        new AttributePath(
                            "id",
                            "number",
                            $this->types
                        )
                    )
                )
            )
        );

        self::assertEquals($expectedPath, $path);
    }

    public function test_parse_path_with_missing_attribute_info()
    {
        $definition = "/users/{}";

        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
The attribute name is missing:
/users/{}
        ^
MESSAGE
        );

        $this->parser->parse($definition, $this->types);
    }

    public function test_parse_path_with_missing_attribute_name()
    {
        $definition = "/users/{:any}";

        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
The attribute name is missing:
/users/{:any}
        ^
MESSAGE
        );

        $this->parser->parse($definition, $this->types);
    }

    public function test_parse_path_with_too_long_attribute_name()
    {
        $definition = "/users/{wayTooLongAttributeNameNoOneShouldNeed:any}";

        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
The attribute name exceeded maximum allowed length of 32 characters:
/users/{wayTooLongAttributeNameNoOneShouldNeed:any}
                                             ^
MESSAGE
        );

        $this->parser->parse($definition, $this->types);
    }

    public function test_parse_path_with_malformed_attribute_name()
    {
        $definition = "/users/{i%d:any}";

        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
Unexpected character (expected ':', '}', 'alphanumeric'):
/users/{i%d:any}
         ^
MESSAGE
        );

        $this->parser->parse($definition, $this->types);
    }

    public function test_parse_path_with_missing_attribute_type()
    {
        $definition = "/users/{id:}";

        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
The attribute type is missing:
/users/{id:}
           ^
MESSAGE
        );

        $this->parser->parse($definition, $this->types);
    }

    public function test_parse_path_with_malformed_attribute_type()
    {
        $definition = "/users/{id:a%ny}";

        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
Unexpected character (expected '}', 'alphanumeric'):
/users/{id:a%ny}
            ^
MESSAGE
        );

        $this->parser->parse($definition, $this->types);
    }

    public function test_parse_path_with_too_long_attribute_type()
    {
        $definition = "/users/{id:wayTooLongAttributeTypeNoOneShouldNeed}";

        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
The attribute type exceeded maximum allowed length of 32 characters:
/users/{id:wayTooLongAttributeTypeNoOneShouldNeed}
                                                ^
MESSAGE
        );

        $this->parser->parse($definition, $this->types);
    }

    public function test_parse_path_with_missing_left_attribute_brace()
    {
        $definition = "/users/id}";

        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
Unexpected character:
/users/id}
         ^
MESSAGE
        );

        $this->parser->parse($definition, $this->types);
    }

    public function test_parse_path_with_missing_right_attribute_brace()
    {
        $definition = "/users/{id";

        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
Unexpected end of route:
/users/{id
          ^
MESSAGE
        );

        $this->parser->parse($definition, $this->types);
    }

    public function test_parse_path_with_missing_left_optional_bracket()
    {
        $definition = "/users/{id}]";

        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
Unexpected character:
/users/{id}]
           ^
MESSAGE
        );

        $this->parser->parse($definition, $this->types);
    }

    public function test_parse_path_with_missing_right_optional_bracket()
    {
        $definition = "/users[/{id}";

        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
Unexpected end of route:
/users[/{id}
            ^
MESSAGE
        );

        $this->parser->parse($definition, $this->types);
    }

    public function test_parse_path_with_optional_sequence_in_the_middle()
    {
        $definition = "/users[/{id}]/{name}";

        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
Optional sequence cannot be followed by anything else:
/users[/{id}]/{name}
             ^
MESSAGE
        );

        $this->parser->parse($definition, $this->types);
    }

    public function test_parse_path_with_mixed_brackets_one()
    {
        $definition = "/users[/{id]}";

        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
Unexpected character (expected ':', '}', 'alphanumeric'):
/users[/{id]}
           ^
MESSAGE
        );

        $this->parser->parse($definition, $this->types);
    }

    public function test_parse_path_with_mixed_brackets_two()
    {
        $definition = "/users/{id[ing}]";

        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
Unexpected character (expected ':', '}', 'alphanumeric'):
/users/{id[ing}]
          ^
MESSAGE
        );

        $this->parser->parse($definition, $this->types);
    }

    public function test_parse_path_with_duplicate_attributes()
    {
        $definition = "/users/{id}/{id:number}";

        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
Multiple attributes with name 'id':
/users/{id}/{id:number}
MESSAGE
        );

        $this->parser->parse($definition, $this->types);
    }

    public function test_parse_path_with_invalid_type()
    {
        $definition = "/users/{id:none}";

        $this->expectException(InvalidRoute::class);
        $this->expectExceptionMessage(<<<MESSAGE
Unknown type 'none' of attribute 'id':
/users/{id:none}
MESSAGE
        );

        $this->parser->parse($definition, $this->types);
    }
}
