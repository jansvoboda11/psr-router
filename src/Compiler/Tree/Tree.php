<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler\Tree;

/**
 * Tree representation of RouteCollection. Its nodes are parts of route paths
 * that might be shared by multiple individual routes.
 */
class Tree extends LeavesGatheringNode implements TreeNode
{
    /**
     * Root nodes.
     *
     * @var TreeNode[]
     */
    private $nodes;

    /**
     * Constructor.
     *
     * @param TreeNode[] $nodes
     */
    public function __construct(array $nodes = [])
    {
        $this->nodes = $nodes;
    }

    /**
     * @inheritdoc
     */
    public function addChild(TreeNode $child): void
    {
        $this->nodes[] = $child;
    }

    /**
     * @inheritdoc
     */
    public function accept(TreeVisitor $visitor): void
    {
        $visitor->enterTree($this);

        foreach ($this->nodes as $node) {
            $node->accept($visitor);
        }

        $visitor->leaveTree($this);
    }

    /**
     * @inheritdoc
     */
    public function getChildren(): array
    {
        return $this->nodes;
    }

    /**
     * @inheritdoc
     */
    public function equals(TreeNode $node): bool
    {
        return $node instanceof self;
    }
}
