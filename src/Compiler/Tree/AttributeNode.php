<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler\Tree;

use Svoboda\Router\Types\Type;

/**
 * Node representing a shared attribute part of the route path.
 */
class AttributeNode extends AbstractNode implements TreeNode
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
     * Constructor.
     *
     * @param string $name
     * @param Type $type
     * @param TreeNode[] $children
     */
    public function __construct(string $name, Type $type, array $children = [])
    {
        parent::__construct($children);

        $this->name = $name;
        $this->type = $type;
    }

    /**
     * Returns the type pattern.
     *
     * @return string
     */
    public function getTypePattern(): string
    {
        return $this->type->getPattern();
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
}
