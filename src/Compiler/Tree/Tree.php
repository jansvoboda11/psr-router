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
    public function __construct(array $nodes)
    {
        $this->nodes = $nodes;
    }

    /**
     * Creates new instance with the given node.
     *
     * @param TreeNode $node
     * @return Tree
     */
    public function withNode(TreeNode $node): self
    {
        $nodes = $this->nodes;

        $nodes[] = $node;

        return new self($nodes);
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
}
