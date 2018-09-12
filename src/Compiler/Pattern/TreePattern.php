<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler\Pattern;

use Svoboda\Router\Compiler\Tree\AttributeNode;
use Svoboda\Router\Compiler\Tree\LeafNode;
use Svoboda\Router\Compiler\Tree\OptionalNode;
use Svoboda\Router\Compiler\Tree\StaticNode;
use Svoboda\Router\Compiler\Tree\Tree;
use Svoboda\Router\Compiler\Tree\TreeNode;
use Svoboda\Router\Compiler\Tree\TreeVisitor;

class TreePattern extends TreeVisitor
{
    private $pattern;

    private $childCounts;

    public function __construct(Tree $tree)
    {
        $this->childCounts = [];

        $this->pattern = "#^";
        $tree->accept($this);
        $this->pattern .= "$#";
    }

    public function enterTree(Tree $tree): void
    {
        $this->maybeStartOrGroup($tree);
    }

    public function leaveTree(Tree $tree): void
    {
        $this->maybeEndOrGroup();
    }

    public function enterAttribute(AttributeNode $node): void
    {
        $this->maybeInsertInOrGroup();

        $pattern = $node->getTypePattern();

        $this->pattern .= "($pattern)";

        $this->maybeStartOrGroup($node);
    }

    public function leaveAttribute(AttributeNode $node): void
    {
        $this->maybeEndOrGroup();
    }

    public function enterOptional(OptionalNode $node): void
    {
        $this->maybeInsertInOrGroup();

        $node->skipToLeaves($this);

        $this->pattern .= "(?:";
    }

    public function leaveOptional(OptionalNode $node): void
    {
        $this->pattern .= ")?";
    }

    public function enterStatic(StaticNode $node): void
    {
        $this->maybeInsertInOrGroup();

        $this->pattern .= $node->getStatic();

        $this->maybeStartOrGroup($node);
    }

    public function leaveStatic(StaticNode $node): void
    {
        $this->maybeEndOrGroup();
    }

    public function enterLeaf(LeafNode $node): void
    {
        $this->maybeInsertInOrGroup();

        $method = $node->getRoute()->getMethod();
        $index = $node->getIndex();

        $this->pattern .= "{}$method(*MARK:$index)";
    }

    private function maybeStartOrGroup(TreeNode $node): void
    {
        $childCount = count($node->getChildren());

        if ($childCount > 1) {
            $this->pattern .= "(?";
        }

        $this->childCounts[] = $childCount;
    }

    private function maybeInsertInOrGroup(): void
    {
        $previousChildCount = $this->childCounts[count($this->childCounts) - 1];

        if ($previousChildCount > 1) {
            $this->pattern .= "|";
        }
    }

    private function maybeEndOrGroup(): void
    {
        $childCount = array_pop($this->childCounts);

        if ($childCount > 1) {
            $this->pattern .= ")";
        }
    }

    public function __toString(): string
    {
        return $this->pattern;
    }
}
