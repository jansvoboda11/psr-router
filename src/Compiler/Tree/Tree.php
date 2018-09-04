<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler\Tree;

/**
 * Tree representation of RouteCollection. Its nodes are parts of route paths
 * that might be shared by multiple individual routes.
 */
class Tree extends AbstractNode implements TreeNode
{
    /**
     * @inheritdoc
     */
    public function accept(TreeVisitor $visitor): void
    {
        $visitor->enterTree($this);

        foreach ($this->children as $child) {
            $child->accept($visitor);
        }

        $visitor->leaveTree($this);
    }
}
