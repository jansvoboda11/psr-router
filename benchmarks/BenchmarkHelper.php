<?php

declare(strict_types=1);

namespace SvobodaBench\Router;

use Faker\Factory as Faker;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ServerRequestInterface;
use Svoboda\Router\Compiler\Code\LinearCodeFactory;
use Svoboda\Router\Compiler\Code\TreeCodeFactory;
use Svoboda\Router\Compiler\Compiler;
use Svoboda\Router\Compiler\MultiPatternCompiler;
use Svoboda\Router\Compiler\Pattern\PathPatternFactory;
use Svoboda\Router\Compiler\Pattern\TreePatternFactory;
use Svoboda\Router\Compiler\PhpCodeCompiler;
use Svoboda\Router\Compiler\SinglePatternCompiler;
use Svoboda\Router\Compiler\Tree\TreeFactory;
use Svoboda\Router\Compiler\TreePatternCompiler;
use Svoboda\Router\Generator\UriGenerator;
use Svoboda\Router\Route\Path\PathSerializer;
use Svoboda\Router\RouteCollection;
use SvobodaTest\Router\FakeHandler;

/**
 * Methods useful for benchmarking.
 */
trait BenchmarkHelper
{
    protected static function createMultiPatternCompiler(): Compiler
    {
        return new MultiPatternCompiler(new PathPatternFactory());
    }

    protected static function createSinglePatternCompiler(): Compiler
    {
        return new SinglePatternCompiler(new PathPatternFactory());
    }

    protected static function createTreePatternCompiler(): Compiler
    {
        return new TreePatternCompiler(new TreeFactory(new PathSerializer()), new TreePatternFactory());
    }

    protected static function createLinearCodeCompiler(): Compiler
    {
        return new PhpCodeCompiler(new LinearCodeFactory());
    }

    protected static function createTreeCodeCompiler(): Compiler
    {
        return new PhpCodeCompiler(new TreeCodeFactory(new TreeFactory(new PathSerializer())));
    }

    protected static function createRoutes(int $count): RouteCollection
    {
        $routes = RouteCollection::create();
        $faker = Faker::create();

        $methods = ["GET", "POST", "PATCH", "DELETE"];

        $pathCount = $count / count($methods);

        $words = array_map(function () use ($faker) {
            return [$faker->word, $faker->word];
        }, range(1, $pathCount));

        foreach ($words as $pathIndex => $pair) {
            [$word1, $word2] = $pair;

            foreach ($methods as $methodIndex => $method) {
                $path = "/api/v2/$word1/{id}/$word2/{name}";
                $index = $pathIndex * count($methods) + $methodIndex;

                $routes->route($method, $path, new FakeHandler(), (string)$index);
            }
        }

        return $routes;
    }

    protected static function createFirstRouteRequest(RouteCollection $routes): ServerRequestInterface
    {
        $faker = Faker::create();

        $generator = UriGenerator::create($routes);

        $firstUri = $generator->generate("0", [
            "id" => $faker->randomNumber(),
            "name" => $faker->word,
        ]);

        return (new Psr17Factory())->createServerRequest("GET", $firstUri);
    }

    protected static function createLastRouteRequest(RouteCollection $routes): ServerRequestInterface
    {
        $faker = Faker::create();

        $generator = UriGenerator::create($routes);

        $name = (string)($routes->count() - 1);

        $lastUri = $generator->generate($name, [
            "id" => $faker->randomNumber(),
            "name" => $faker->word,
        ]);

        return (new Psr17Factory())->createServerRequest("DELETE", $lastUri);
    }

    protected static function createNoRouteRequest()
    {
        return (new Psr17Factory())->createServerRequest("GET", "/api/v2/does/not/exist");
    }

    protected static function showRoutes(RouteCollection $routes): void
    {
        foreach ($routes->all() as $index => $route) {
            $method = $route->getMethod();
            $definition = $route->getPath()->getDefinition();

            var_dump("[$index] $method:$definition");
        }
    }

    protected static function showRequests(array $requests): void
    {
        foreach ($requests as $name => $request) {
            $method = $request->getMethod();
            $uri = $request->getUri()->getPath();

            var_dump("[$name] $method:$uri");
        }
    }
}
