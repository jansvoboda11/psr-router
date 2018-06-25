<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Compiler;

use Svoboda\PsrRouter\RouteCollection;

/**
 * Creates an array of individual regular expressions for each route.
 */
class NaiveCompiler implements Compiler
{
    /**
     * @var NaiveRegexVisitor
     */
    private $visitor;

    /**
     * @param null|NaiveRegexVisitor $visitor
     */
    public function __construct(?NaiveRegexVisitor $visitor = null)
    {
        $this->visitor = $visitor ?? new NaiveRegexVisitor();
    }

    /**
     * @inheritdoc
     */
    public function compile(RouteCollection $routes, CompilationContext $context): Matcher
    {
        $rs = [];

        foreach ($routes as $route) {
            $method = $route->getMethod();
            $ast = $route->getAst();

            $pathPattern = $this->visitor->createRegex($ast, $context);

            $pattern = "#^" . $method . $pathPattern . "$#";

            $rs[] = [$pattern, $route];
        }

        return new NaiveMatcher($rs);
    }
}
