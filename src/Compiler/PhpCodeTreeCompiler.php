<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler;

use Svoboda\Router\Compiler\Paths\PathsCodeFactory;
use Svoboda\Router\Compiler\Tree\TreeFactory;
use Svoboda\Router\Matcher\Matcher;
use Svoboda\Router\RouteCollection;

/**
 * Compiles the route collection into a matcher made of native PHP code with a tree structure.
 */
class PhpCodeTreeCompiler implements Compiler
{
    /**
     * Tree factory.
     *
     * @var TreeFactory
     */
    private $treeFactory;

    /**
     * Code factory.
     *
     * @var PathsCodeFactory
     */
    private $codeFactory;

    public function __construct(TreeFactory $treeFactory, PathsCodeFactory $codeFactory)
    {
        $this->treeFactory = $treeFactory;
        $this->codeFactory = $codeFactory;
    }

    /**
     * @inheritdoc
     */
    public function compile(RouteCollection $routes): Matcher
    {
        $tree = $this->treeFactory->create($routes);

        $class = "PhpCodeTreeMatcher" . mt_rand(0, PHP_INT_MAX);

        $code = $this->codeFactory->create($tree, $class);

        eval($code);

        return new $class($routes);
    }
}
