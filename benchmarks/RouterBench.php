<?php

declare(strict_types=1);

use Svoboda\PsrRouter\Compiler\Context;
use Svoboda\PsrRouter\Compiler\MultiPatternCompiler;
use Svoboda\PsrRouter\Router;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Uri;

class RouterBench
{
    private $request;

    private $naiveRouter;

    public function __construct()
    {
        $routes = require __DIR__ . "/config/routes.php";
        $compiler = new MultiPatternCompiler();
        $context = Context::createDefault();

        $this->naiveRouter = new Router($routes, $compiler, $context);

        $this->request = (new ServerRequest())->withUri(new Uri("/"));
    }

    /**
     * @Iterations(5)
     * @Revs(1000)
     */
    public function benchNaiveRouter()
    {
        $this->naiveRouter->match($this->request);
    }
}
