<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler;

use Svoboda\Router\Compiler\Path\PathCodeFactory;
use Svoboda\Router\Matcher\Matcher;
use Svoboda\Router\Route\Route;
use Svoboda\Router\RouteCollection;

/**
 * Compiles the route collection into a matcher made of native PHP code.
 */
class PhpCodeCompiler implements Compiler
{
    /**
     * The code factory.
     *
     * @var PathCodeFactory
     */
    private $codeFactory;

    /**
     * Constructor.
     *
     * @param PathCodeFactory $codeFactory
     */
    public function __construct(PathCodeFactory $codeFactory)
    {
        $this->codeFactory = $codeFactory;
    }

    /**
     * @inheritdoc
     */
    public function compile(RouteCollection $routes): Matcher
    {
        // todo: make use of proper template engine

        $routeCodes = array_map(function (int $index, Route $route): string {
            return $this->codeFactory->create($route, $index);
        }, range(0, $routes->count() - 1), $routes->all());

        $routesCode = implode("", $routeCodes);

        $class = "PhpCodeMatcher" . mt_rand(0, PHP_INT_MAX);

        $code = <<<CODE
use Psr\Http\Message\ServerRequestInterface;
use Svoboda\Router\Matcher\AbstractMatcher;
use Svoboda\Router\Failure;
use Svoboda\Router\Match;
use Svoboda\Router\RouteCollection;

class $class extends AbstractMatcher
{
    private \$routes;

    public function __construct(RouteCollection \$routes)
    {
        \$this->routes = \$routes;
    }

    public function match(ServerRequestInterface \$request): Match
    {
        \$matches = [];
        \$allowed = [];

        \$index = \$this->matchInner(\$request, \$matches, \$allowed);

        if (\$index === null) {
            throw new Failure(\$allowed, \$request);
        }

        \$route = \$this->routes->all()[\$index];

        return \$this->createResult(\$route, \$request, \$matches);
    }

    private function matchInner(ServerRequestInterface \$request, array &\$matches, array &\$allowed): ?int
    {
        \$path = \$request->getUri()->getPath();
        \$method = \$request->getMethod();

        $routesCode

        return null;
    }
}
CODE;

        eval($code);

        return new $class($routes);
    }
}
