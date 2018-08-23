<?php

declare(strict_types=1);

use Nyholm\Psr7\Factory\Psr17Factory;
use Svoboda\Router\Compiler\MultiPatternCompiler;
use Svoboda\Router\Compiler\PatternFactory;
use Svoboda\Router\Compiler\SinglePatternCompiler;
use Svoboda\Router\Failure;
use Svoboda\Router\Router;

class RouterBench
{
    private $multiPatternRouter;
    private $singlePatternRouter;

    private $requestToFirstRoute;
    private $requestToLastRoute;
    private $requestToNoRoute;

    public function __construct()
    {
        $routes = require __DIR__ . "/config/routes.php";

        $this->multiPatternRouter = new Router($routes, new MultiPatternCompiler(new PatternFactory()));
        $this->singlePatternRouter = new Router($routes, new SinglePatternCompiler(new PatternFactory()));

        $this->requestToFirstRoute = (new Psr17Factory())->createServerRequest("GET", "/");
        $this->requestToLastRoute = (new Psr17Factory())->createServerRequest("DELETE", "/orders/123");
        $this->requestToNoRoute = (new Psr17Factory())->createServerRequest("GET", "/does/not/exist");
    }

    /**
     * @Iterations(10)
     * @Revs(1000)
     */
    public function benchFirstRouteMultiPattern()
    {
        $this->multiPatternRouter->match($this->requestToFirstRoute);
    }

    /**
     * @Iterations(10)
     * @Revs(1000)
     */
    public function benchFirstRouteSinglePattern()
    {
        $this->singlePatternRouter->match($this->requestToFirstRoute);
    }

    /**
     * @Iterations(10)
     * @Revs(1000)
     */
    public function benchLastRouteMultiPattern()
    {
        $this->multiPatternRouter->match($this->requestToLastRoute);
    }

    /**
     * @Iterations(10)
     * @Revs(1000)
     */
    public function benchLastRouteSinglePattern()
    {
        $this->singlePatternRouter->match($this->requestToLastRoute);
    }

    /**
     * @Iterations(10)
     * @Revs(1000)
     */
    public function benchNoRouteMultiPattern()
    {
        try {
            $this->multiPatternRouter->match($this->requestToNoRoute);
        } catch (Failure $failure) {
            //
        }
    }

    /**
     * @Iterations(10)
     * @Revs(1000)
     */
    public function benchNoRouteSinglePattern()
    {
        try {
            $this->singlePatternRouter->match($this->requestToNoRoute);
        } catch (Failure $failure) {
            //
        }
    }
}
