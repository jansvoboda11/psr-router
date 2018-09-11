<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit\Generator;

use Svoboda\Router\Generator\InvalidAttribute;
use Svoboda\Router\Generator\PathUri;
use Svoboda\Router\Route\Path\AttributePath;
use Svoboda\Router\Route\Path\OptionalPath;
use Svoboda\Router\Route\Path\StaticPath;
use Svoboda\Router\Types\Type;
use SvobodaTest\Router\TestCase;

class PathUriTest extends TestCase
{
    /** @var Type */
    private $any;

    /** @var Type */
    private $number;

    protected function setUp()
    {
        $this->any = new Type("any", "[^/]+");
        $this->number = new Type("number", "\d+");
    }

    public function test_static_uri_is_generated()
    {
        $path = new StaticPath("/home");

        $uri = new PathUri($path);

        self::assertEquals("/home", $uri);
    }

    public function test_uri_with_single_attribute_is_generated()
    {
        $path = new StaticPath("/users/",
            new AttributePath("id", $this->number)
        );

        $uri = new PathUri($path, [
            "id" => 42,
        ]);

        self::assertEquals("/users/42", $uri);
    }

    public function test_uri_with_multiple_attributes_is_generated()
    {
        $path = new StaticPath("/users/",
            new AttributePath("id", $this->number,
                new StaticPath("/",
                    new AttributePath("name", $this->any
                    )
                )
            )
        );

        $uri = new PathUri($path, [
            "name" => "jansvoboda11",
            "id" => 42,
        ]);

        self::assertEquals("/users/42/jansvoboda11", $uri);
    }

    public function test_uri_with_optional_argument_is_generated()
    {
        $path = new StaticPath("/users/",
            new AttributePath("id", $this->number,
                new OptionalPath(
                    new StaticPath("/",
                        new AttributePath("name", $this->any)
                    )
                )
            )
        );

        $uri = new PathUri($path, [
            "name" => "jansvoboda11",
            "id" => 42,
        ]);

        self::assertEquals("/users/42/jansvoboda11", $uri);
    }

    public function test_optional_static_suffix_is_ignored()
    {
        $path = new StaticPath("/users/",
            new AttributePath("id", $this->number,
                new OptionalPath(
                    new StaticPath("/edit")
                )
            )
        );

        $uri = new PathUri($path, [
            "id" => 42,
        ]);

        self::assertEquals("/users/42", $uri);
    }

    public function test_optional_attribute_suffix_is_ignored()
    {
        $path = new StaticPath("/users/",
            new AttributePath("id", $this->number,
                new OptionalPath(
                    new StaticPath("/",
                        new AttributePath("name", $this->any)
                    )
                )
            )
        );

        $uri = new PathUri($path, [
            "id" => 42,
        ]);

        self::assertEquals("/users/42", $uri);
    }

    public function test_non_existent_attribute_is_ignored()
    {
        $path = new StaticPath("/users/",
            new AttributePath("id", $this->any)
        );

        $uri = new PathUri($path, [
            "foo" => "bar",
            "id" => 42,
        ]);

        self::assertEquals("/users/42", $uri);
    }

    public function test_missing_required_attribute_causes_failure()
    {
        $path = new StaticPath("/users/",
            new AttributePath("id", $this->number)
        );

        $this->expectException(InvalidAttribute::class);
        $this->expectExceptionMessage("The value for attribute 'id' is missing");

        new PathUri($path);
    }

    public function test_missing_preceding_optional_attribute_causes_failure()
    {
        $path = new StaticPath("/users/",
            new AttributePath("id", $this->number,
                new OptionalPath(
                    new StaticPath("/",
                        new AttributePath("first", $this->any,
                            new OptionalPath(
                                new StaticPath("/",
                                    new AttributePath("last", $this->any)
                                )
                            )
                        )
                    )
                )
            )
        );

        $this->expectException(InvalidAttribute::class);
        $this->expectExceptionMessage("The value for attribute 'first' is missing");

        new PathUri($path, [
            "id" => 42,
            "last" => "Svoboda",
        ]);
    }

    public function test_attribute_type_mismatch_causes_failure()
    {
        $path = new StaticPath("/users/",
            new AttributePath("id", $this->number)
        );

        $this->expectException(InvalidAttribute::class);
        $this->expectExceptionMessage("The value 'i42' of attribute 'id' does not match the specified pattern: \d+");

        new PathUri($path, [
            "id" => "i42",
        ]);
    }
}
