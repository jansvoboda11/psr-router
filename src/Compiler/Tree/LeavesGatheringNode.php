<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler\Tree;

/**
 * Abstract node performing recursive gathering of leaves.
 */
abstract class LeavesGatheringNode implements TreeNode
{
    /**
     * Gathers leaves of the sub-tree.
     *
     * @return LeafNode[]
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
