<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler\Tree;

/**
 * Abstract node with children.
 */
abstract class AbstractNode implements TreeNode
{
    /**
     * Child nodes.
     *
     * @var TreeNode[]
     */
    protected $children;

    /**
     * Constructor.
     *
     * @param array $children
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
     * @inheritdoc
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @inheritdoc
     */
    public function withoutChildren(): TreeNode
    {
        $clone = clone $this;

        $clone->children = [];

        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function gatherLeaves(): array
    {
        $leaves = [];

        foreach ($this->getChildren() as $child) {
            $leaves = array_merge($leaves, $child->gatherLeaves());
        }

        return $leaves;
    }
}
