<?php

declare(strict_types=1);

namespace SvobodaBench\Router;

use Psr\Http\Message\ServerRequestInterface;
use Svoboda\Router\Failure;
use Svoboda\Router\RouteCollection;
use Svoboda\Router\Router;

/**
 * @Iterations(100)
 * @Revs({500})
 */
class RouterBench
{
    use BenchmarkHelper;

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
        $this->routes = $this->createRoutes(500);

        $this->routers = [
            self::ROUTER_MULTI_PATTERN => new Router($this->routes, self::createMultiPatternCompiler()),
            self::ROUTER_SINGLE_PATTERN => new Router($this->routes, self::createSinglePatternCompiler()),
            self::ROUTER_TREE_PATTERN => new Router($this->routes, self::createTreePatternCompiler()),
            self::ROUTER_LINEAR_CODE => new Router($this->routes, self::createLinearCodeCompiler()),
            self::ROUTER_TREE_CODE => new Router($this->routes, self::createTreeCodeCompiler()),
        ];

        $this->requests = [
            self::REQUEST_FIRST_ROUTE => self::createFirstRouteRequest($this->routes),
            self::REQUEST_LAST_ROUTE => self::createLastRouteRequest($this->routes),
            self::REQUEST_NO_ROUTE => self::createNoRouteRequest(),
        ];

//        self::showRoutes($this->routes);
//        self::showRequests($this->requests);
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
}
