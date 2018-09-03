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
    public function __construct(array $children)
    {
        $this->children = $children;
    }

    /**
     * Creates new instance with the given child.
     *
     * @param TreeNode $child
     * @return OptionalNode
     */
    public function withChild(TreeNode $child): self
    {
        $children = $this->children;

        $children[] = $child;

        return new self($children);
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
}
