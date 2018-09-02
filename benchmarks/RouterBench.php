<?php

declare(strict_types=1);

use Nyholm\Psr7\Factory\Psr17Factory;
use Svoboda\Router\Compiler\MultiPatternCompiler;
use Svoboda\Router\Compiler\Path\PathCodeFactory;
use Svoboda\Router\Compiler\Path\PathPatternFactory;
use Svoboda\Router\Compiler\PhpCodeCompiler;
use Svoboda\Router\Compiler\SinglePatternCompiler;
use Svoboda\Router\Failure;
use Svoboda\Router\Router;

class RouterBench
{
    private $multiPatternRouter;
    private $singlePatternRouter;
    private $phpCodeRouter;

    private $requestToFirstRoute;
    private $requestToLastRoute;
    private $requestToNoRoute;

    public function __construct()
    {
        $routes = require __DIR__ . "/config/routes.php";

        $this->multiPatternRouter = new Router($routes, new MultiPatternCompiler(new PathPatternFactory()));
        $this->singlePatternRouter = new Router($routes, new SinglePatternCompiler(new PathPatternFactory()));
        $this->phpCodeRouter = new Router($routes, new PhpCodeCompiler(new PathCodeFactory()));

        $this->requestToFirstRoute = (new Psr17Factory())->createServerRequest("GET", "/");
        $this->requestToLastRoute = (new Psr17Factory())->createServerRequest("DELETE", "/orders/123");
        $this->requestToNoRoute = (new Psr17Factory())->createServerRequest("GET", "/does/not/exist");
    }

    /**
     * @Groups({"first route"})
     * @Iterations(30)
     * @Revs(10000)
     */
    public function benchFirstRouteMultiPattern()
    {
        $this->multiPatternRouter->match($this->requestToFirstRoute);
    }

    /**
     * @Groups({"first route"})
     * @Iterations(30)
     * @Revs(10000)
     */
    public function benchFirstRouteSinglePattern()
    {
        $this->singlePatternRouter->match($this->requestToFirstRoute);
    }

    /**
     * @Groups({"first route"})
     * @Iterations(30)
     * @Revs(10000)
     */
    public function benchFirstRoutePhpCode()
    {
        $this->phpCodeRouter->match($this->requestToFirstRoute);
    }

    /**
     * @Groups({"last route"})
     * @Iterations(30)
     * @Revs(10000)
     */
    public function benchLastRouteMultiPattern()
    {
        $this->multiPatternRouter->match($this->requestToLastRoute);
    }

    /**
     * @Groups({"last route"})
     * @Iterations(30)
     * @Revs(10000)
     */
    public function benchLastRouteSinglePattern()
    {
        $this->singlePatternRouter->match($this->requestToLastRoute);
    }

    /**
     * @Groups({"last route"})
     * @Iterations(30)
     * @Revs(10000)
     */
    public function benchLastRoutePhpCode()
    {
        $this->phpCodeRouter->match($this->requestToLastRoute);
    }

    /**
     * @Groups({"no route"})
     * @Iterations(30)
     * @Revs(10000)
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
     * @Groups({"no route"})
     * @Iterations(30)
     * @Revs(10000)
     */
    public function benchNoRouteSinglePattern()
    {
        try {
            $this->singlePatternRouter->match($this->requestToNoRoute);
        } catch (Failure $failure) {
            //
        }
    }

    /**
     * @Groups({"no route"})
     * @Iterations(30)
     * @Revs(10000)
     */
    public function benchNoRoutePhpCode()
    {
        try {
            $this->phpCodeRouter->match($this->requestToNoRoute);
        } catch (Failure $failure) {
            //
        }
    }
}
