<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler\Tree;

/**
 * Node representing a shared static part of the path.
 */
class StaticNode extends AbstractNode implements TreeNode
{
    /**
     * Static path.
     *
     * @var string
     */
    private $static;

    /**
     * Constructor.
     *
     * @param string $static
     * @param TreeNode[] $children
     */
    public function __construct(string $static, array $children = [])
    {
        parent::__construct($children);

        $this->static = $static;
    }

    /**
     * Returns the static string.
     *
     * @return string
     */
    public function getStatic(): string
    {
        return $this->static;
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
}
