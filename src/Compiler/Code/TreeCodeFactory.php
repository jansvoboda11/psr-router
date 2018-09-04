<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler\Code;

use Svoboda\Router\Compiler\Tree\TreeFactory;
use Svoboda\Router\RouteCollection;

/**
 * Creates tree PHP code for routes.
 */
class TreeCodeFactory implements RoutesCodeFactory
{
    /**
     * Tree factory.
     *
     * @var TreeFactory
     */
    private $treeFactory;

    /**
     * Constructor.
     *
     * @param TreeFactory $treeFactory
     */
    public function __construct(TreeFactory $treeFactory)
    {
        $this->treeFactory = $treeFactory;
    }

    /**
     * @inheritdoc
     */
    public function create(RouteCollection $routes): string
    {
        $tree = $this->treeFactory->create($routes);

        $pathsCode = new TreeCode($tree);

        return (string)$pathsCode;
    }
}
