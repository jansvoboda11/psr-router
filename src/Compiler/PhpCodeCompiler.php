<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler;

use Svoboda\Router\Compiler\Code\CollectionCodeFactory;
use Svoboda\Router\Matcher\Matcher;
use Svoboda\Router\RouteCollection;

/**
 * Compiles the route collection into a matcher made of native PHP code.
 */
class PhpCodeCompiler implements Compiler
{
    /**
     * The code factory.
     *
     * @var CollectionCodeFactory
     */
    private $codeFactory;

    /**
     * Constructor.
     *
     * @param CollectionCodeFactory $codeFactory
     */
    public function __construct(CollectionCodeFactory $codeFactory)
    {
        $this->codeFactory = $codeFactory;
    }

    /**
     * @inheritdoc
     */
    public function compile(RouteCollection $routes): Matcher
    {
        $class = "PhpCodeMatcher" . mt_rand(0, PHP_INT_MAX);

        $routesCode = $this->codeFactory->create($routes);

        $code = $this->createClass($class, $routesCode);

        eval($code);

        return new $class($routes);
    }

    /**
     * Creates a matcher class with given name that matches incoming requests with the provided code.
     *
     * @param string $class
     * @param string $routesCode
     * @return string
     */
    private function createClass(string $class, string $routesCode): string
    {
        return <<<CODE
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
    }
}
