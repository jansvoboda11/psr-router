<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler\Tree;

/**
 * Two-pass visitor of all node types of the route tree.
 */
abstract class TreeVisitor
{
    /**
     * Enters the tree.
     *
     * @param Tree $tree
     */
    public function enterTree(Tree $tree): void
    {
        //
    }

    /**
     * Leaves the tree.
     *
     * @param Tree $tree
     */
    public function leaveTree(Tree $tree): void
    {
        //
    }

    /**
     * Enters the attribute node.
     *
     * @param AttributeNode $node
     */
    public function enterAttribute(AttributeNode $node): void
    {
        //
    }

    /**
     * Leaves the attribute node.
     *
     * @param AttributeNode $node
     */
    public function leaveAttribute(AttributeNode $node): void
    {
        //
    }

    /**
     * Enters the optional node.
     *
     * @param OptionalNode $node
     */
    public function enterOptional(OptionalNode $node): void
    {
        //
    }

    /**
     * Leaves the optional node.
     *
     * @param OptionalNode $node
     */
    public function leaveOptional(OptionalNode $node): void
    {
        //
    }

    /**
     * Enters the static node.
     *
     * @param StaticNode $node
     */
    public function enterStatic(StaticNode $node): void
    {
        //
    }

    /**
     * Leaves the static node.
     *
     * @param StaticNode $node
     */
    public function leaveStatic(StaticNode $node): void
    {
        //
    }

    /**
     * Enters the leaf node.
     *
     * @param Leaf $node
     */
    public function enterLeaf(Leaf $node): void
    {
        //
    }

    /**
     * Leaves the leaf node.
     *
     * @param Leaf $node
     */
    public function leaveLeaf(Leaf $node): void
    {
        //
    }
}
