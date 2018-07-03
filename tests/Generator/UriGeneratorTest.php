<?php

declare(strict_types=1);

namespace SvobodaTest\PsrRouter\Generator;

use Svoboda\PsrRouter\Generator\InvalidAttribute;
use Svoboda\PsrRouter\Generator\RouteNotFound;
use Svoboda\PsrRouter\RouteCollection;
use Svoboda\PsrRouter\Generator\UriGenerator;
use SvobodaTest\PsrRouter\TestCase;

class UriGeneratorTest extends TestCase
{
    public function test_it_generates_static_uri()
    {
        $routes = new RouteCollection();
        $routes->get("/home", "UsersAction", "home");

        $uri = UriGenerator::create($routes)->generate("home");

        self::assertEquals("/home", $uri);
    }

    public function test_it_generates_uri_with_single_attribute()
    {
        $routes = new RouteCollection();
        $routes->get("/users/{id:num}", "UserAction", "users.detail");

        $uri = UriGenerator::create($routes)->generate("users.detail", ["id" => 42]);

        self::assertEquals("/users/42", $uri);
    }

    public function test_it_generates_uri_with_multiple_attributes()
    {
        $routes = new RouteCollection();
        $routes->get("/users/{id:num}/{name}", "UserAction", "users.detail");

        $uri = UriGenerator::create($routes)->generate("users.detail", [
            "name" => "jansvoboda11",
            "id" => 42,
        ]);

        self::assertEquals("/users/42/jansvoboda11", $uri);
    }

    public function test_it_generates_uri_with_optional_argument()
    {
        $routes = new RouteCollection();
        $routes->get("/users/{id:num}[/{name}]", "UserAction", "users.detail");

        $uri = UriGenerator::create($routes)->generate("users.detail", [
            "name" => "jansvoboda11",
            "id" => 42,
        ]);

        self::assertEquals("/users/42/jansvoboda11", $uri);
    }

    public function test_it_ignores_optional_static_suffix()
    {
        $routes = new RouteCollection();
        $routes->get("/users/{id:num}[/edit]", "UserAction", "users.detail");

        $uri = UriGenerator::create($routes)->generate("users.detail", [
            "id" => 42,
        ]);

        self::assertEquals("/users/42", $uri);
    }

    public function test_it_ignores_optional_attribute_suffix()
    {
        $routes = new RouteCollection();
        $routes->get("/users/{id:num}[/{name}]", "UserAction", "users.detail");

        $uri = UriGenerator::create($routes)->generate("users.detail", [
            "id" => 42,
        ]);

        self::assertEquals("/users/42", $uri);
    }

    public function test_it_fails_on_missing_required_attribute()
    {
        $routes = new RouteCollection();
        $routes->get("/users/{id:num}", "UserAction", "users.detail");

        $this->expectException(InvalidAttribute::class);

        UriGenerator::create($routes)->generate("users.detail");
    }

    public function test_it_fails_on_missing_preceding_optional_attribute()
    {
        $routes = new RouteCollection();
        $routes->get("/users/{id:num}[/{first}[/{last}]]", "UserAction", "users.detail");

        $this->expectException(InvalidAttribute::class);

        UriGenerator::create($routes)->generate("users.detail", [
            "id" => 42,
            "last" => "Svoboda",
        ]);
    }

    public function test_it_fails_on_non_existent_attribute()
    {
        $routes = new RouteCollection();
        $routes->get("/users/{id:num}", "UserAction", "users.detail");

        $this->expectException(InvalidAttribute::class);

        UriGenerator::create($routes)->generate("users.detail", [
            "foo" => "bar",
        ]);
    }

    public function test_it_fails_on_attribute_type_mismatch()
    {
        $routes = new RouteCollection();
        $routes->get("/users/{id:num}", "UserAction", "users.detail");

        $this->expectException(InvalidAttribute::class);

        UriGenerator::create($routes)->generate("users.detail", [
            "id" => "undefined",
        ]);
    }

    public function test_it_fails_on_non_existent_route_name()
    {
        $routes = new RouteCollection();
        $routes->get("/users", "UsersAction", "users.all");

        $this->expectException(RouteNotFound::class);

        UriGenerator::create($routes)->generate("users.detail");
    }
}
