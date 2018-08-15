<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit\Generator;

use Svoboda\Router\Generator\InvalidAttribute;
use Svoboda\Router\Generator\PathUri;
use Svoboda\Router\Route\Path\AttributePath;
use Svoboda\Router\Route\Path\OptionalPath;
use Svoboda\Router\Route\Path\StaticPath;
use Svoboda\Router\Types\TypeCollection;
use SvobodaTest\Router\TestCase;

class PathUriTest extends TestCase
{
    /** @var TypeCollection */
    private $types;

    protected function setUp()
    {
        $this->types = TypeCollection::createDefault();
    }

    public function test_it_generates_static_uri()
    {
        $path = new StaticPath("/home");

        $uri = new PathUri($path);

        self::assertEquals("/home", $uri);
    }

    public function test_it_generates_uri_with_single_attribute()
    {
        $path = new StaticPath(
            "/users/",
            new AttributePath(
                "id",
                "number",
                $this->types
            )
        );

        $uri = new PathUri($path, [
            "id" => 42,
        ]);

        self::assertEquals("/users/42", $uri);
    }

    public function test_it_generates_uri_with_multiple_attributes()
    {
        $path = new StaticPath(
            "/users/",
            new AttributePath(
                "id",
                "number",
                $this->types,
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

        $uri = new PathUri($path, [
            "name" => "jansvoboda11",
            "id" => 42,
        ]);

        self::assertEquals("/users/42/jansvoboda11", $uri);
    }

    public function test_it_generates_uri_with_optional_argument()
    {
        $path = new StaticPath(
            "/users/",
            new AttributePath(
                "id",
                "number",
                $this->types,
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
            )
        );

        $uri = new PathUri($path, [
            "name" => "jansvoboda11",
            "id" => 42,
        ]);

        self::assertEquals("/users/42/jansvoboda11", $uri);
    }

    public function test_it_ignores_optional_static_suffix()
    {
        $path = new StaticPath(
            "/users/",
            new AttributePath(
                "id",
                "number",
                $this->types,
                new OptionalPath(
                    new StaticPath(
                        "/edit"
                    )
                )
            )
        );

        $uri = new PathUri($path, [
            "id" => 42,
        ]);

        self::assertEquals("/users/42", $uri);
    }

    public function test_it_ignores_optional_attribute_suffix()
    {
        $path = new StaticPath(
            "/users/",
            new AttributePath(
                "id",
                "number",
                $this->types,
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
            )
        );

        $uri = new PathUri($path, [
            "id" => 42,
        ]);

        self::assertEquals("/users/42", $uri);
    }

    public function test_it_ignores_non_existent_attribute()
    {
        $path = new StaticPath(
            "/users/",
            new AttributePath(
                "id",
                null,
                $this->types
            )
        );

        $uri = new PathUri($path, [
            "foo" => "bar",
            "id" => 42,
        ]);

        self::assertEquals("/users/42", $uri);
    }

    public function test_it_fails_on_missing_required_attribute()
    {
        $path = new StaticPath(
            "/users/",
            new AttributePath(
                "id",
                "number",
                $this->types
            )
        );

        $this->expectException(InvalidAttribute::class);
        $this->expectExceptionMessage(
            "The value for attribute 'id' is missing"
        );

        new PathUri($path);
    }

    public function test_it_fails_on_missing_preceding_optional_attribute()
    {
        $path = new StaticPath(
            "/users/",
            new AttributePath(
                "id",
                "number",
                $this->types,
                new OptionalPath(
                    new StaticPath(
                        "/",
                        new AttributePath(
                            "first",
                            null,
                            $this->types,
                            new OptionalPath(
                                new StaticPath(
                                    "/",
                                    new AttributePath(
                                        "last",
                                        null,
                                        $this->types
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );

        $this->expectException(InvalidAttribute::class);
        $this->expectExceptionMessage(
            "The value for attribute 'first' is missing"
        );

        new PathUri($path, [
            "id" => 42,
            "last" => "Svoboda",
        ]);
    }

    public function test_it_fails_on_attribute_type_mismatch()
    {
        $path = new StaticPath(
            "/users/",
            new AttributePath(
                "id",
                "number",
                $this->types
            )
        );

        $this->expectException(InvalidAttribute::class);
        $this->expectExceptionMessage(
            "The value 'i42' of attribute 'id' does not match the specified pattern: \d+"
        );

        new PathUri($path, [
            "id" => "i42",
        ]);
    }
}
