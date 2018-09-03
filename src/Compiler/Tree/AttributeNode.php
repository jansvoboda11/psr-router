<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler\Tree;

use Svoboda\Router\Types\Type;

/**
 * Node representing a shared attribute part of the route path.
 */
class AttributeNode extends LeavesGatheringNode implements TreeNode
{
    /**
     * Attribute name.
     *
     * @var string
     */
    private $name;

    /**
     * Attribute type.
     *
     * @var Type
     */
    private $type;

    /**
     * Child nodes.
     *
     * @var TreeNode[]
     */
    private $children;

    /**
     * Constructor.
     *
     * @param string $name
     * @param Type $type
     * @param TreeNode[] $children
     */
    public function __construct(string $name, Type $type, array $children)
    {
        $this->name = $name;
        $this->type = $type;
        $this->children = $children;
    }

    /**
     * Creates new instance with the given child.
     *
     * @param TreeNode $child
     * @return AttributeNode
     */
    public function withChild(TreeNode $child): self
    {
        $children = $this->children;

        $children[] = $child;

        return new self($this->name, $this->type, $children);
    }

    /**
     * @inheritdoc
     */
    public function accept(TreeVisitor $visitor): void
    {
        $visitor->enterAttribute($this);

        foreach ($this->children as $child) {
            $child->accept($visitor);
        }

        $visitor->leaveAttribute($this);
    }

    /**
     * @inheritdoc
     */
    public function getChildren(): array
    {
        return $this->getChildren();
    }
}
