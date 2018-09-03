<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler\Tree;

/**
 * Node representing a shared static part of the path.
 */
class StaticNode extends LeavesGatheringNode implements TreeNode
{
    /**
     * Static path.
     *
     * @var string
     */
    private $static;

    /**
     * Child nodes.
     *
     * @var TreeNode[]
     */
    private $children;

    /**
     * Constructor.
     *
     * @param string $static
     * @param TreeNode[] $children
     */
    public function __construct(string $static, array $children = [])
    {
        $this->static = $static;
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
     * @inheritdoc
     */
    public function accept(TreeVisitor $visitor): void
    {
        $visitor->enterStatic($this);

        foreach ($this->children as $child) {
            $child->accept($visitor);
        }

        $visitor->leaveStatic($this);
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
        return $node instanceof self
            && $this->static === $node->static;
    }
}
