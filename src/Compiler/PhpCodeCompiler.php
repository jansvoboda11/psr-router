<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler;

use Svoboda\Router\Compiler\Path\PathCode;
use Svoboda\Router\Matcher\Matcher;
use Svoboda\Router\RouteCollection;

/**
 * Compiles the route collection into a matcher made of native PHP code.
 */
class PhpCodeCompiler implements Compiler
{
    /**
     * @inheritdoc
     */
    public function compile(RouteCollection $routes): Matcher
    {
        // todo: make use of proper template engine

        $class = "PhpCodeMatcher" . mt_rand(0, PHP_INT_MAX);

        $code = <<<CODE
class $class extends \Svoboda\Router\Matcher\AbstractMatcher
{
    private \$routes;
    
    public function __construct(\Svoboda\Router\RouteCollection \$routes)
    {
        \$this->routes = \$routes;
    }
    
    public function match(\Psr\Http\Message\ServerRequestInterface \$request): \Svoboda\Router\Match
    {
        \$m = [];
        \$a = [];
        
        \$index = \$this->matchInner(\$request, \$m, \$a);
        
        if (\$index === null) {
            throw new \Svoboda\Router\Failure(\$a, \$request);
        }
        
        \$route = \$this->routes->all()[\$index];
        
        return \$this->createResult(\$route, \$request, \$m);
    }
    
    public function matchInner(\Psr\Http\Message\ServerRequestInterface \$request, array&\$matches, array &\$allowed): ?int
    {
    \$path = \$request->getUri()->getPath();
    \$method = \$request->getMethod();

CODE;

        foreach ($routes->all() as $index => $route) {
            $code .= (new PathCode($route, $index));
        }

        $code .= "return null;}}";

        eval($code);

        return new $class($routes);
    }
}
