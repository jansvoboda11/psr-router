<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit\Route;

use Svoboda\Router\Route\Method;
use SvobodaTest\Router\TestCase;

class MethodTest extends TestCase
{
    public function test_it_returns_all_methods()
    {
        $methods = Method::all();

        self::assertContains("OPTIONS", $methods);
        self::assertContains("GET", $methods);
        self::assertContains("HEAD", $methods);
        self::assertContains("POST", $methods);
        self::assertContains("PUT", $methods);
        self::assertContains("PATCH", $methods);
        self::assertContains("DELETE", $methods);
    }

    public function test_it_recognizes_valid_methods()
    {
        self::assertTrue(Method::isValid("OPTIONS"));
        self::assertTrue(Method::isValid("GET"));
        self::assertTrue(Method::isValid("HEAD"));
        self::assertTrue(Method::isValid("POST"));
        self::assertTrue(Method::isValid("PUT"));
        self::assertTrue(Method::isValid("PATCH"));
        self::assertTrue(Method::isValid("DELETE"));
    }

    public function test_it_recognizes_invalid_methods()
    {
        self::assertFalse(Method::isValid("INVALID"));
    }

    public function test_it_recognizes_always_allowed_methods()
    {
        self::assertTrue(Method::isAlwaysAllowed("GET"));
        self::assertTrue(Method::isAlwaysAllowed("HEAD"));
    }

    public function test_it_recognizes_method_not_always_allowed()
    {
        self::assertFalse(Method::isAlwaysAllowed("POST"));
    }
}
