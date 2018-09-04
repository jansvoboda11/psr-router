<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler\Tree;

/**
 * Represents one part of the route path that may be shared by multiple routes.
 */
interface TreeNode
{
    /**
     * Accepts the visitor and allows it to visit the children nodes.
     *
     * @param TreeVisitor $visitor
     */
    public function accept(TreeVisitor $visitor): void;

    /**
     * Adds new child to the node.
     *
     * @param TreeNode $child
     */
    public function addChild(TreeNode $child): void;

    /**
     * Returns all direct children of the node.
     *
     * @return TreeNode[]
     */
    public function getChildren(): array;

    /**
     * Returns copy of the tree node without children.
     *
     * @return TreeNode
     */
    public function withoutChildren(): self;

    /**
     * Gathers leaves of the sub-tree.
     *
     * @return Leaf[]
     */
    public function gatherLeaves(): array;
}
