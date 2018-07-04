<?php

declare(strict_types=1);

namespace SvobodaTest\PsrRouter\Generator;

use Svoboda\PsrRouter\Compiler\Context;
use Svoboda\PsrRouter\Generator\InvalidAttribute;
use Svoboda\PsrRouter\Generator\UriBuilder;
use Svoboda\PsrRouter\Route\Path\AttributePath;
use Svoboda\PsrRouter\Route\Path\OptionalPath;
use Svoboda\PsrRouter\Route\Path\StaticPath;
use SvobodaTest\PsrRouter\TestCase;

class UriBuilderTest extends TestCase
{
    /** @var UriBuilder */
    private $builder;

    protected function setUp()
    {
        $context = new Context([
            "any" => "[^/]+",
            "num" => "\d+",
        ], "any");

        $this->builder = new UriBuilder(
            $context
        );
    }

    public function test_it_generates_static_uri()
    {
        $path = new StaticPath("/home");

        $uri = $this->builder->buildUri($path);

        self::assertEquals("/home", $uri);
    }

    public function test_it_generates_uri_with_single_attribute()
    {
        $path = new StaticPath(
            "/users/",
            new AttributePath(
                "id",
                "num"
            )
        );

        $uri = $this->builder->buildUri($path, [
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
                "num",
                new StaticPath(
                    "/",
                    new AttributePath(
                        "name",
                        null
                    )
                )
            )
        );

        $uri = $this->builder->buildUri($path, [
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
                "num",
                new OptionalPath(
                    new StaticPath(
                        "/",
                        new AttributePath(
                            "name",
                            null
                        )
                    )
                )
            )
        );

        $uri = $this->builder->buildUri($path, [
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
                "num",
                new OptionalPath(
                    new StaticPath(
                        "/edit"
                    )
                )
            )
        );

        $uri = $this->builder->buildUri($path, [
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
                "num",
                new OptionalPath(
                    new StaticPath(
                        "/",
                        new AttributePath(
                            "name",
                            null
                        )
                    )
                )
            )
        );

        $uri = $this->builder->buildUri($path, [
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
                null
            )
        );

        $uri = $this->builder->buildUri($path, [
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
                "num"
            )
        );

        $this->expectException(InvalidAttribute::class);
        $this->expectExceptionMessage(<<<MESSAGE
The value for attribute 'id' is missing
MESSAGE
        );

        $this->builder->buildUri($path);
    }

    public function test_it_fails_on_missing_preceding_optional_attribute()
    {
        $path = new StaticPath(
            "/users/",
            new AttributePath(
                "id",
                "num",
                new OptionalPath(
                    new StaticPath(
                        "/",
                        new AttributePath(
                            "first",
                            null,
                            new OptionalPath(
                                new StaticPath(
                                    "/",
                                    new AttributePath(
                                        "last",
                                        null
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );

        $this->expectException(InvalidAttribute::class);
        $this->expectExceptionMessage(<<<MESSAGE
The value for attribute 'first' is missing
MESSAGE
        );

        $this->builder->buildUri($path, [
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
                "num"
            )
        );

        $this->expectException(InvalidAttribute::class);
        $this->expectExceptionMessage(<<<MESSAGE
The value 'i42' of attribute 'id' does not match the specified pattern: \d+
MESSAGE
        );

        $this->builder->buildUri($path, [
            "id" => "i42",
        ]);
    }
}
