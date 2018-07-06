<?php

declare(strict_types=1);

use Svoboda\Router\Compiler\Context;
use Svoboda\Router\Compiler\MultiPatternCompiler;
use Svoboda\Router\Compiler\PatternBuilder;
use Svoboda\Router\Router;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Uri;

class RouterBench
{
    private $request;

    private $naiveRouter;

    public function __construct()
    {
        $routes = require __DIR__ . "/config/routes.php";
        $context = Context::createDefault();
        $patternBuilder = new PatternBuilder($context);
        $compiler = new MultiPatternCompiler($patternBuilder);

        $this->naiveRouter = new Router($routes, $compiler);

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
