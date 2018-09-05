<?php

declare(strict_types=1);

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ServerRequestInterface;
use Svoboda\Router\Compiler\Code\LinearCodeFactory;
use Svoboda\Router\Compiler\Code\TreeCodeFactory;
use Svoboda\Router\Compiler\MultiPatternCompiler;
use Svoboda\Router\Compiler\Pattern\PathPatternFactory;
use Svoboda\Router\Compiler\Pattern\TreePatternFactory;
use Svoboda\Router\Compiler\PhpCodeCompiler;
use Svoboda\Router\Compiler\SinglePatternCompiler;
use Svoboda\Router\Compiler\Tree\TreeFactory;
use Svoboda\Router\Compiler\TreePatternCompiler;
use Svoboda\Router\Failure;
use Svoboda\Router\Generator\UriGenerator;
use Svoboda\Router\Route\Path\PathSerializer;
use Svoboda\Router\RouteCollection;
use Svoboda\Router\Router;
use SvobodaTest\Router\FakeHandler;

/**
 * @Iterations(100)
 * @Revs({500})
 */
class RouterBench
{
    /** @var RouteCollection */
    private $routes;
    
    /** @var Router[] */
    private $routers;

    /** @var ServerRequestInterface[] */
    private $requests;

    const ROUTER_MULTI_PATTERN = "multi pattern";
    const ROUTER_SINGLE_PATTERN = "single pattern";
    const ROUTER_TREE_PATTERN = "tree pattern";
    const ROUTER_LINEAR_CODE = "linear code";
    const ROUTER_TREE_CODE = "tree code";

    const REQUEST_FIRST_ROUTE = "first route";
    const REQUEST_LAST_ROUTE = "last route";
    const REQUEST_NO_ROUTE = "no route";

    public function __construct()
    {
        $this->routes = $this->createRoutes(600);

        $this->routers = [
            self::ROUTER_MULTI_PATTERN => $this->createMultiPatternRouter($this->routes),
            self::ROUTER_SINGLE_PATTERN => $this->createSinglePatternRouter($this->routes),
            self::ROUTER_TREE_PATTERN => $this->createTreePatternRouter($this->routes),
            self::ROUTER_LINEAR_CODE => $this->createLinearCodeRouter($this->routes),
            self::ROUTER_TREE_CODE => $this->createTreeCodeRouter($this->routes),
        ];

        $this->requests = [
            self::REQUEST_FIRST_ROUTE => $this->createFirstRouteRequest($this->routes),
            self::REQUEST_LAST_ROUTE => $this->createLastRouteRequest($this->routes),
            self::REQUEST_NO_ROUTE => $this->createNoRouteRequest(),
        ];

//        $this->showRoutes();
//        $this->showRequests();
    }

    /**
     * @Groups({"first route"})
     * @ParamProviders({"provideRouters"})
     *
     * @param array $params
     */
    public function bench_first_route(array $params): void
    {
        $this->match($params["router"], self::REQUEST_FIRST_ROUTE);
    }

    /**
     * @Groups({"last route"})
     * @ParamProviders({"provideRouters"})
     *
     * @param array $params
     */
    public function bench_last_route(array $params): void
    {
        $this->match($params["router"], self::REQUEST_LAST_ROUTE);
    }

    /**
     * @Groups({"no route"})
     * @ParamProviders({"provideRouters"})
     *
     * @param array $params
     */
    public function bench_no_route(array $params): void
    {
        $this->match($params["router"], self::REQUEST_NO_ROUTE);
    }

    public function match(string $routerName, string $requestName): void
    {
        /** @var Router $router */
        $router = $this->routers[$routerName];

        /** @var ServerRequestInterface $request */
        $request = $this->requests[$requestName];

        try {
            $router->match($request);
        } catch (Failure $failure) {
            //
        }
    }

    public function provideRouters(): array
    {
        return [
            ["router" => self::ROUTER_MULTI_PATTERN],
            ["router" => self::ROUTER_SINGLE_PATTERN],
            ["router" => self::ROUTER_TREE_PATTERN],
            ["router" => self::ROUTER_LINEAR_CODE],
            ["router" => self::ROUTER_TREE_CODE],
        ];
    }

    public function provideRequests(): array
    {
        return [
            ["request" => self::REQUEST_FIRST_ROUTE],
            ["request" => self::REQUEST_LAST_ROUTE],
            ["request" => self::REQUEST_NO_ROUTE],
        ];
    }

    public function createRoutes(int $count): RouteCollection
    {
        $routes = RouteCollection::create();
        $faker = Faker\Factory::create();

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

    public function createMultiPatternRouter(RouteCollection $routes): Router
    {
        return new Router($routes, new MultiPatternCompiler(new PathPatternFactory()));
    }

    public function createSinglePatternRouter(RouteCollection $routes): Router
    {
        return new Router($routes, new SinglePatternCompiler(new PathPatternFactory()));
    }

    private function createTreePatternRouter(RouteCollection $routes): Router
    {
        return new Router(
            $routes, new TreePatternCompiler(new TreeFactory(new PathSerializer()), new TreePatternFactory())
        );
    }

    private function createLinearCodeRouter(RouteCollection $routes): Router
    {
        return new Router($routes, new PhpCodeCompiler(new LinearCodeFactory()));
    }

    private function createTreeCodeRouter(RouteCollection $routes): Router
    {
        return new Router($routes, new PhpCodeCompiler(new TreeCodeFactory(new TreeFactory(new PathSerializer()))));
    }

    public function createFirstRouteRequest(RouteCollection $routes): ServerRequestInterface
    {
        $faker = Faker\Factory::create();

        $generator = UriGenerator::create($routes);

        $firstUri = $generator->generate("0", [
            "id" => $faker->randomNumber(),
            "name" => $faker->word,
        ]);

        return (new Psr17Factory())->createServerRequest("GET", $firstUri);
    }

    public function createLastRouteRequest(RouteCollection $routes): ServerRequestInterface
    {
        $faker = Faker\Factory::create();

        $generator = UriGenerator::create($routes);

        $name = (string)($routes->count() - 1);

        $lastUri = $generator->generate($name, [
            "id" => $faker->randomNumber(),
            "name" => $faker->word,
        ]);

        return (new Psr17Factory())->createServerRequest("DELETE", $lastUri);
    }

    public function createNoRouteRequest()
    {
        return (new Psr17Factory())->createServerRequest("GET", "/api/v2/does/not/exist");
    }

    public function showRoutes(): void
    {
        foreach ($this->routes->all() as $index => $route) {
            $method = $route->getMethod();
            $definition = $route->getPath()->getDefinition();

            var_dump("[$index] $method:$definition");
        }
    }

    public function showRequests(): void
    {
        foreach ($this->requests as $name => $request) {
            $method = $request->getMethod();
            $uri = $request->getUri()->getPath();

            var_dump("[$name] $method:$uri");
        }
    }
}
