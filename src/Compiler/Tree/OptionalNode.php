<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler\Tree;

/**
 * Node representing shared optional part of the path.
 */
class OptionalNode extends LeavesGatheringNode implements TreeNode
{
    /**
     * Child nodes.
     *
     * @var TreeNode[]
     */
    private $children;

    /**
     * Constructor.
     *
     * @param TreeNode[] $children
     */
    public function __construct(array $children = [])
    {
        $this->children = $children;
    }

    /**
     * @inheritdoc
     */
    public function addChild(TreeNode $child): void
    {
        $this->children[] = $child;
    }

    /**
     * Sends the visitor to the tree leaves, skipping all intermediate nodes.
     *
     * @param TreeVisitor $visitor
     */
    public function skipToLeaves(TreeVisitor $visitor): void
    {
        $leaves = $this->gatherLeaves();

        foreach ($leaves as $leaf) {
            $leaf->accept($visitor);
        }
    }

    /**
     * @inheritdoc
     */
    public function accept(TreeVisitor $visitor): void
    {
        $visitor->enterOptional($this);

        foreach ($this->children as $child) {
            $child->accept($visitor);
        }

        $visitor->leaveOptional($this);
    }

    /**
     * @inheritdoc
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @inheritdoc
     */
    public function equals(TreeNode $node): bool
    {
        return $node instanceof self;
    }
}
