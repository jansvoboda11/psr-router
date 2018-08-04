<?php

declare(strict_types=1);

use Nyholm\Psr7\Factory\Psr17Factory;
use Svoboda\Router\Compiler\MultiPatternCompiler;
use Svoboda\Router\Compiler\PatternFactory;
use Svoboda\Router\Router;

class RouterBench
{
    private $request;

    private $router;

    public function __construct()
    {
        $routes = require __DIR__ . "/config/routes.php";

        $compiler = new MultiPatternCompiler(new PatternFactory());

        $this->router = new Router($routes, $compiler);

        $this->request = (new Psr17Factory())->createServerRequest("GET", "/");
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
