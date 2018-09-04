<?php

declare(strict_types=1);

use Nyholm\Psr7\Factory\Psr17Factory;
use Svoboda\Router\Compiler\Code\LinearCodeFactory;
use Svoboda\Router\Compiler\Code\TreeCodeFactory;
use Svoboda\Router\Compiler\MultiPatternCompiler;
use Svoboda\Router\Compiler\Pattern\PathPatternFactory;
use Svoboda\Router\Compiler\PhpCodeCompiler;
use Svoboda\Router\Compiler\SinglePatternCompiler;
use Svoboda\Router\Compiler\Tree\TreeFactory;
use Svoboda\Router\Failure;
use Svoboda\Router\Route\Path\PathSerializer;
use Svoboda\Router\Router;

class RouterBench
{
    private $multiPatternRouter;
    private $singlePatternRouter;
    private $phpLinearCodeRouter;
    private $phpTreeCodeRouter;

    private $requestToFirstRoute;
    private $requestToLastRoute;
    private $requestToNoRoute;

    public function __construct()
    {
        $routes = require __DIR__ . "/config/routes.php";

        $this->multiPatternRouter = new Router($routes, new MultiPatternCompiler(new PathPatternFactory()));
        $this->singlePatternRouter = new Router($routes, new SinglePatternCompiler(new PathPatternFactory()));
        $this->phpLinearCodeRouter = new Router($routes, new PhpCodeCompiler(new LinearCodeFactory()));
        $this->phpTreeCodeRouter = new Router($routes, new PhpCodeCompiler(new TreeCodeFactory(new TreeFactory(new PathSerializer()))));

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
    public function benchFirstRouteLinearCode()
    {
        $this->phpLinearCodeRouter->match($this->requestToFirstRoute);
    }

    /**
     * @Groups({"first route"})
     * @Iterations(30)
     * @Revs(10000)
     */
    public function benchFirstRouteTreeCode()
    {
        $this->phpTreeCodeRouter->match($this->requestToFirstRoute);
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
    public function benchLastRouteLinearCode()
    {
        $this->phpLinearCodeRouter->match($this->requestToLastRoute);
    }

    /**
     * @Groups({"last route"})
     * @Iterations(30)
     * @Revs(10000)
     */
    public function benchLastRouteTreeCode()
    {
        $this->phpTreeCodeRouter->match($this->requestToLastRoute);
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
    public function benchNoRouteLinearCode()
    {
        try {
            $this->phpLinearCodeRouter->match($this->requestToNoRoute);
        } catch (Failure $failure) {
            //
        }
    }

    /**
     * @Groups({"no route"})
     * @Iterations(30)
     * @Revs(10000)
     */
    public function benchNoRouteTreeCode()
    {
        try {
            $this->phpTreeCodeRouter->match($this->requestToNoRoute);
        } catch (Failure $failure) {
            //
        }
    }
}
