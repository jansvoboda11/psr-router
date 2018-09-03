<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler\Tree;

use Svoboda\Router\Route\Path\AttributePath;
use Svoboda\Router\Route\Path\OptionalPath;
use Svoboda\Router\Route\Path\PathSerializer;
use Svoboda\Router\Route\Path\RoutePath;
use Svoboda\Router\Route\Path\StaticPath;
use Svoboda\Router\Route\Route;
use Svoboda\Router\RouteCollection;

/**
 * Tree factory.
 */
class TreeFactory
{
    /**
     * Path serializer.
     *
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
        $routesArray = $routes->all();

        $routesPaths = array_map(function (Route $route) {
            return $this->serializer->serialize($route->getPath());
        }, $routesArray);

        $tree = new Tree();

        /** @var RoutePath[] $pathArray */
        foreach ($routesPaths as $index => $pathArray) {
            $route = $routesArray[$index];

            /** @var TreeNode $treeNode */
            $treeNode = $tree;

            foreach ($pathArray as $path) {
                $treeNode = $this->addPathPartToTree($treeNode, $path, $route, $index);
            }
        }

        return $tree;
    }

    /**
     * Adds route path to the given node. Merges with existing one,
     * if possible, otherwise creates new tree node.
     *
     * @param TreeNode $node
     * @param RoutePath $path
     * @param Route $route
     * @param int $index
     * @return TreeNode
     */
    private function addPathPartToTree(TreeNode $node, RoutePath $path, Route $route, int $index): TreeNode
    {
        $pathNode = $this->pathToNode($path, $route, $index);

        foreach ($node->getChildren() as $child) {
            if ($pathNode->equals($child)) {
                return $child;
            }
        }

        $node->addChild($pathNode);

        return $pathNode;
    }

    /**
     * Converts the given route path to a tree node.
     *
     * @param RoutePath $path
     * @param Route $route
     * @param int $index
     * @return TreeNode
     */
    private function pathToNode(RoutePath $path, Route $route, int $index): TreeNode
    {
        if ($path instanceof AttributePath) {
            return $this->createAttributeNode($path);
        }

        if ($path instanceof StaticPath) {
            return $this->createStaticNode($path);
        }

        if ($path instanceof OptionalPath) {
            return $this->createOptionalNode();
        }

        return $this->createLeafNode($route, $index);
    }

    /**
     * Creates new attribute node from attribute path.
     *
     * @param AttributePath $path
     * @return AttributeNode
     */
    private function createAttributeNode(AttributePath $path): AttributeNode
    {
        return new AttributeNode($path->getName(), $path->getType());
    }

    /**
     * Creates new static node from static path.
     *
     * @param StaticPath $path
     * @return StaticNode
     */
    private function createStaticNode(StaticPath $path): StaticNode
    {
        return new StaticNode($path->getStatic());
    }

    /**
     * Creates new optional node.
     *
     * @return OptionalNode
     */
    private function createOptionalNode(): OptionalNode
    {
        return new OptionalNode();
    }

    /**
     * Creates new leaf node.
     *
     * @param Route $route
     * @param int $index
     * @return LeafNode
     */
    private function createLeafNode(Route $route, int $index): LeafNode
    {
        return new LeafNode($route, $index);
    }
}
