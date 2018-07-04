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
    public function test_it_generates_static_uri()
    {
        $path = new StaticPath("/home");

        $uri = (new UriBuilder())->buildUri($path, [], self::context());

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

        $uri = (new UriBuilder())->buildUri($path, [
            "id" => 42,
        ], self::context());

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

        $uri = (new UriBuilder())->buildUri($path, [
            "name" => "jansvoboda11",
            "id" => 42,
        ], self::context());

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

        $uri = (new UriBuilder())->buildUri($path, [
            "name" => "jansvoboda11",
            "id" => 42,
        ], self::context());

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

        $uri = (new UriBuilder())->buildUri($path, [
            "id" => 42,
        ], self::context());

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

        $uri = (new UriBuilder())->buildUri($path, [
            "id" => 42,
        ], self::context());

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

        $uri = (new UriBuilder())->buildUri($path, [
            "foo" => "bar",
            "id" => 42,
        ], self::context());

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

        (new UriBuilder())->buildUri($path, [], self::context());
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

        (new UriBuilder())->buildUri($path, [
            "id" => 42,
            "last" => "Svoboda",
        ], self::context());
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

        (new UriBuilder())->buildUri($path, [
            "id" => "i42",
        ], self::context());
    }

    /**
     * Returns the testing context.
     *
     * @return Context
     */
    private static function context(): Context
    {
        return new Context([
            "any" => "[^/]+",
            "num" => "\d+",
        ], "any");
    }
}
