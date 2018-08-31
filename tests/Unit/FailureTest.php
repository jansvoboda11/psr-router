<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit;

use Svoboda\Router\Failure;
use Svoboda\Router\Route\Path\StaticPath;
use Svoboda\Router\Route\Route;
use SvobodaTest\Router\FakeHandler;
use SvobodaTest\Router\TestCase;

class FailureTest extends TestCase
{
    public function test_uri_failure_is_recognized()
    {
        $request = self::createRequest("GET", "/");

        $failure = new Failure([], $request);

        self::assertFalse($failure->isMethodFailure());
    }

    public function test_uri_failure_has_not_uri_handlers()
    {
        $request = self::createRequest("GET", "/");

        $failure = new Failure([], $request);

        self::assertEmpty($failure->getUriRoutes());
    }

    public function test_uri_failure_has_no_allowed_methods()
    {
        $request = self::createRequest("GET", "/");

        $failure = new Failure([], $request);

        self::assertEmpty($failure->getAllowedMethods());
    }

    public function test_uri_failure_does_not_allow_any_method()
    {
        $request = self::createRequest("GET", "/");

        $failure = new Failure([], $request);

        self::assertFalse($failure->isMethodAllowed("GET"));
        self::assertFalse($failure->isMethodAllowed("POST"));
    }

    public function test_uri_failure_does_not_return_any_uri_handler_for_all_methods()
    {
        $request = self::createRequest("GET", "/");

        $failure = new Failure([], $request);

        self::assertNull($failure->getUriRouteFor("GET"));
        self::assertNull($failure->getUriRouteFor("POST"));
    }

    public function test_method_failure_is_recognized()
    {
        $request = self::createRequest("GET", "/");

        $postRoute = new Route("GET", new StaticPath("/"), new FakeHandler());
        $deleteRoute = new Route("DELETE", new StaticPath("/"), new FakeHandler());

        $failure = new Failure([
            "POST" => $postRoute,
            "DELETE" => $deleteRoute,
        ], $request);

        self::assertTrue($failure->isMethodFailure());
    }

    public function test_method_failure_returns_uri_handlers()
    {
        $request = self::createRequest("GET", "/");

        $postRoute = new Route("GET", new StaticPath("/"), new FakeHandler());
        $deleteRoute = new Route("DELETE", new StaticPath("/"), new FakeHandler());

        $failure = new Failure([
            "POST" => $postRoute,
            "DELETE" => $deleteRoute,
        ], $request);

        self::assertCount(2, $failure->getUriRoutes());
        self::assertContains($postRoute, $failure->getUriRoutes());
        self::assertContains($deleteRoute, $failure->getUriRoutes());
    }

    public function test_method_failure_returns_allowed_methods()
    {
        $request = self::createRequest("GET", "/");

        $postRoute = new Route("GET", new StaticPath("/"), new FakeHandler());
        $deleteRoute = new Route("DELETE", new StaticPath("/"), new FakeHandler());

        $failure = new Failure([
            "POST" => $postRoute,
            "DELETE" => $deleteRoute,
        ], $request);

        self::assertCount(2, $failure->getAllowedMethods());
        self::assertContains("POST", $failure->getAllowedMethods());
        self::assertContains("DELETE", $failure->getAllowedMethods());
    }

    public function test_method_failure_allows_matched_methods()
    {
        $request = self::createRequest("GET", "/");

        $postRoute = new Route("GET", new StaticPath("/"), new FakeHandler());
        $deleteRoute = new Route("DELETE", new StaticPath("/"), new FakeHandler());

        $failure = new Failure([
            "POST" => $postRoute,
            "DELETE" => $deleteRoute,
        ], $request);

        self::assertTrue($failure->isMethodAllowed("POST"));
        self::assertFalse($failure->isMethodAllowed("PUT"));
    }

    public function test_method_failure_returns_method_handler()
    {
        $request = self::createRequest("GET", "/");

        $postRoute = new Route("GET", new StaticPath("/"), new FakeHandler());
        $deleteRoute = new Route("DELETE", new StaticPath("/"), new FakeHandler());

        $failure = new Failure([
            "POST" => $postRoute,
            "DELETE" => $deleteRoute,
        ], $request);


        self::assertEquals($postRoute, $failure->getUriRouteFor("POST"));
    }

    public function test_it_returns_request()
    {
        $request = self::createRequest("GET", "/");

        $failure = new Failure([], $request);

        self::assertEquals($request, $failure->getRequest());
    }
}
