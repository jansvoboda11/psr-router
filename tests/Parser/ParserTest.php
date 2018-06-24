<?php

declare(strict_types=1);

namespace SvobodaTest\PsrRouter\Parser;

use PHPUnit\Framework\TestCase;
use Svoboda\PsrRouter\Parser\Parser;
use Svoboda\PsrRouter\InvalidRoute;
use Svoboda\PsrRouter\Route;

class ParserTest extends TestCase
{
    /** @var Parser */
    private $parser;

    public function setUp()
    {
        $this->parser = new Parser();
    }

    public function test_static_path()
    {
        $route = new Route("GET", "/users/all", "Action");

        $parsed = $this->parser->parse($route);

        self::assertEquals("GET", $parsed->getMethod());
        self::assertEquals("Action", $parsed->getHandlerName());
        self::assertEquals("/users/all", $parsed->rebuildDefinition());
        self::assertEquals([], $parsed->gatherAttributes());
    }

    public function test_path_with_attribute_without_type()
    {
        $route = new Route("GET", "/users/{id}", "Action");

        $parsed = $this->parser->parse($route);

        self::assertEquals("GET", $parsed->getMethod());
        self::assertEquals("Action", $parsed->getHandlerName());
        self::assertEquals("/users/{id}", $parsed->rebuildDefinition());
        self::assertEquals([
            "id" => ["type" => null, "required" => true],
        ], $parsed->gatherAttributes());
    }

    public function test_path_with_attribute_of_all_type()
    {
        $route = new Route("GET", "/users/{id:any}", "Action");

        $parsed = $this->parser->parse($route);

        self::assertEquals("GET", $parsed->getMethod());
        self::assertEquals("Action", $parsed->getHandlerName());
        self::assertEquals("/users/{id:any}", $parsed->rebuildDefinition());
        self::assertEquals([
            "id" => ["type" => "any", "required" => true],
        ], $parsed->gatherAttributes());
    }

    public function test_path_with_multiple_attributes()
    {
        $route = new Route("GET", "/users/{name}/{id:num}", "Action");

        $parsed = $this->parser->parse($route);

        self::assertEquals("GET", $parsed->getMethod());
        self::assertEquals("Action", $parsed->getHandlerName());
        self::assertEquals("/users/{name}/{id:num}", $parsed->rebuildDefinition());
        self::assertEquals([
            "name" => ["type" => null, "required" => true],
            "id" => ["type" => "num", "required" => true],
        ], $parsed->gatherAttributes());
    }

    public function test_path_with_optional_attribute()
    {
        $route = new Route("GET", "/users[/{name}]", "Action");

        $parsed = $this->parser->parse($route);

        self::assertEquals("GET", $parsed->getMethod());
        self::assertEquals("Action", $parsed->getHandlerName());
        self::assertEquals("/users[/{name}]", $parsed->rebuildDefinition());
        self::assertEquals([
            "name" => ["type" => null, "required" => false],
        ], $parsed->gatherAttributes());
    }

    public function test_path_with_required_and_optional_attributes()
    {
        $route = new Route("GET", "/users/{name}[/{id:num}]", "Action");

        $parsed = $this->parser->parse($route);

        self::assertEquals("GET", $parsed->getMethod());
        self::assertEquals("Action", $parsed->getHandlerName());
        self::assertEquals("/users/{name}[/{id:num}]", $parsed->rebuildDefinition());
        self::assertEquals([
            "name" => ["type" => null, "required" => true],
            "id" => ["type" => "num", "required" => false],
        ], $parsed->gatherAttributes());
    }

    public function test_path_with_missing_attribute_info()
    {
        $this->expectException(InvalidRoute::class);

        $route = new Route("GET", "/users/{}", "Action");

        $this->parser->parse($route);
    }

    public function test_path_with_missing_attribute_name()
    {
        $this->expectException(InvalidRoute::class);

        $route = new Route("GET", "/users/{:any}", "Action");

        $this->parser->parse($route);
    }

    public function test_path_with_too_long_attribute_name()
    {
        $this->expectException(InvalidRoute::class);

        $route = new Route("GET", "/users/{wayTooLongAttributeNameNoOneShouldNeed:any}", "Action");

        $this->parser->parse($route);
    }

    public function test_path_with_malformed_attribute_name()
    {
        $this->expectException(InvalidRoute::class);

        $route = new Route("GET", "/users/{i%d:any}", "Action");

        $this->parser->parse($route);
    }

    public function test_path_with_missing_attribute_type()
    {
        $this->expectException(InvalidRoute::class);

        $route = new Route("GET", "/users/{id:}", "Action");

        $this->parser->parse($route);
    }

    public function test_path_with_malformed_attribute_type()
    {
        $this->expectException(InvalidRoute::class);

        $route = new Route("GET", "/users/{id:a%ny}", "Action");

        $this->parser->parse($route);
    }

    public function test_path_with_too_long_attribute_type()
    {
        $this->expectException(InvalidRoute::class);

        $route = new Route("GET", "/users/{id:wayTooLongAttributeTypeNoOneShouldNeed}", "Action");

        $this->parser->parse($route);
    }

    public function test_path_with_missing_left_attribute_brace()
    {
        $this->expectException(InvalidRoute::class);

        $route = new Route("GET", "/users/id}", "Action");

        $this->parser->parse($route);
    }

    public function test_path_with_missing_right_attribute_brace()
    {
        $this->expectException(InvalidRoute::class);

        $route = new Route("GET", "/users/{id", "Action");

        $this->parser->parse($route);
    }

    public function test_path_with_missing_left_optional_bracket()
    {
        $this->expectException(InvalidRoute::class);

        $route = new Route("GET", "/users/{id}]", "Action");

        $this->parser->parse($route);
    }

    public function test_path_with_missing_right_optional_bracket()
    {
        $this->expectException(InvalidRoute::class);

        $route = new Route("GET", "/users[/{id}", "Action");

        $this->parser->parse($route);
    }

    public function test_path_with_optional_sequence_in_the_middle()
    {
        $this->expectException(InvalidRoute::class);

        $route = new Route("GET", "/users[/{id}]/{name}", "Action");

        $this->parser->parse($route);
    }

    public function test_path_with_mixed_brackets_one()
    {
        $this->expectException(InvalidRoute::class);

        $route = new Route("GET", "/users[/{id]}", "Action");

        $this->parser->parse($route);
    }

    public function test_path_with_mixed_brackets_two()
    {

        $this->expectException(InvalidRoute::class);

        $route = new Route("GET", "/users/{id[ing}]", "Action");

        $this->parser->parse($route);
    }

    public function test_path_with_two_required_attributes_of_same_name()
    {
        $this->expectException(InvalidRoute::class);

        $route = new Route("GET", "/users/{name}/{name}", "Action");

        $parsed = $this->parser->parse($route);

        $parsed->gatherAttributes();
    }

    public function test_path_with_required_and_optional_attribute_of_same_name()
    {
        $this->expectException(InvalidRoute::class);

        $route = new Route("GET", "/users/{name}[/{name}]", "Action");

        $parsed = $this->parser->parse($route);

        $parsed->gatherAttributes();
    }
}
