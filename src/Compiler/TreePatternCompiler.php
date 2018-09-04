<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler;

use Svoboda\Router\Compiler\Pattern\TreePatternFactory;
use Svoboda\Router\Compiler\Tree\TreeFactory;
use Svoboda\Router\Matcher\Matcher;
use Svoboda\Router\Matcher\SinglePatternMatcher;
use Svoboda\Router\RouteCollection;

/**
 * Creates a regular expression pattern organised in a tree-like manner.
 */
class TreePatternCompiler implements Compiler
{
    /**
     * Tree factory.
     *
     * @var TreeFactory
     */
    private $treeFactory;

    /**
     * Pattern factory.
     *
     * @var TreePatternFactory
     */
    private $patternFactory;

    /**
     * Constructor.
     *
     * @param TreeFactory $treeFactory
     * @param TreePatternFactory $patternFactory
     */
    public function __construct(TreeFactory $treeFactory, TreePatternFactory $patternFactory)
    {
        $this->treeFactory = $treeFactory;
        $this->patternFactory = $patternFactory;
    }

    /**
     * @inheritdoc
     */
    public function compile(RouteCollection $routes): Matcher
    {
        $tree = $this->treeFactory->create($routes);

        $pattern = $this->patternFactory->create($tree);

        return new SinglePatternMatcher($pattern, $routes->all());
    }
}
