<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler\Tree;

use Svoboda\Router\Route\Path\PathSerializer;
use Svoboda\Router\Route\Path\RoutePath;
use Svoboda\Router\Route\Route;
use Svoboda\Router\RouteCollection;

/**
 * Tree factory.
 */
class TreeFactory
{
    /**
     * @var PathSerializer
     */
    private $serializer;

    /**
     * Constructor.
     *
     * @param PathSerializer $serializer
     */
    public function __construct(PathSerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Creates new tree from the route collection.
     *
     * @param RouteCollection $routes
     * @return Tree
     */
    public function create(RouteCollection $routes): Tree
    {
        $routesPaths = array_map(function (Route $route) {
            return $this->serializer->serialize($route->getPath());
        }, $routes->all());

        $tree = new Tree([]);

        /** @var RoutePath[] $path */
        foreach ($routesPaths as $path) {
            /** @var TreeNode $treeNode */
            $treeNode = $tree;

            foreach ($path as $part) {
                $this->addPathPartToTree();
            }
        }

        // todo: implement

        return new Tree([]);
    }

    private function addPathPartToTree()
    {

    }
}
