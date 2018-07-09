<?php

declare(strict_types=1);

use Svoboda\Router\Compiler\MultiPatternCompiler;
use Svoboda\Router\Compiler\PatternBuilder;
use Svoboda\Router\Router;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Uri;

class RouterBench
{
    private $request;

    private $router;

    public function __construct()
    {
        $routes = require __DIR__ . "/config/routes.php";

        $compiler = new MultiPatternCompiler(new PatternBuilder());

        $this->router = new Router($routes, $compiler);

        $this->request = (new ServerRequest())->withUri(new Uri("/"));
    }

    /**
     * @Iterations(5)
     * @Revs(1000)
     */
    public function benchMultiPatternMatcher()
    {
        $this->router->match($this->request);
    }
}
