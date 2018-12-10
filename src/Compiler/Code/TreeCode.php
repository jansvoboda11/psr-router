<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler\Code;

use Svoboda\Router\Compiler\Tree\AttributeNode;
use Svoboda\Router\Compiler\Tree\LeafNode;
use Svoboda\Router\Compiler\Tree\OptionalNode;
use Svoboda\Router\Compiler\Tree\StaticNode;
use Svoboda\Router\Compiler\Tree\Tree;
use Svoboda\Router\Compiler\Tree\TreeVisitor;

/**
 * PHP code structured as a tree that performs matching of the incoming request against all routes.
 */
class TreeCode extends TreeVisitor
{
    /**
     * The generated matcher code.
     *
     * @var string
     */
    private $code;

    /**
     * The tree nesting level.
     *
     * @var int
     */
    private $nesting;

    /**
     * Constructor.
     *
     * @param Tree $tree
     */
    public function __construct(Tree $tree)
    {
        $this->code = "";
        $this->nesting = 0;

        $tree->accept($this);
    }

    /**
     * @inheritdoc
     */
    public function enterTree(Tree $tree): void
    {
        $this->enter();

        $this->code .= <<<CODE

            \$uri = \$path;
            \$matches = [];

            CODE;
    }

    /**
     * @inheritdoc
     */
    public function leaveTree(Tree $tree): void
    {
        $this->leave();
    }

    /**
     * @inheritdoc
     */
    public function enterAttribute(AttributeNode $node): void
    {
        $this->enter();

        $pattern = $node->getTypePattern();

        $this->code .= <<<CODE

            // attribute path
            
            \$uri{$this->nesting} = \$uri;
            \$matches{$this->nesting} = \$matches;
            
            if (preg_match("#^($pattern)#", \$uri, \$ms) === 1) {
                \$matches[] = \$ms[1];
                \$uri = substr(\$uri, strlen(\$ms[1]));

            CODE;
    }

    /**
     * @inheritdoc
     */
    public function leaveAttribute(AttributeNode $node): void
    {
        $this->code .= <<<CODE
            
            }
            
            \$uri = \$uri{$this->nesting};
            \$matches = \$matches{$this->nesting};
            
            // attribute path end

            CODE;

        $this->leave();
    }

    /**
     * @inheritdoc
     */
    public function enterOptional(OptionalNode $node): void
    {
        $this->enter();

        $this->code .= <<<CODE

            // optional path

            CODE;

        $node->skipToLeaves($this);
    }

    /**
     * @inheritdoc
     */
    public function leaveOptional(OptionalNode $node): void
    {
        $node->skipToLeaves($this);

        $this->code .= <<<CODE

            // optional path end

            CODE;

        $this->leave();
    }

    /**
     * @inheritdoc
     */
    public function enterStatic(StaticNode $node): void
    {
        $this->enter();

        $static = $node->getStatic();
        $staticLength = strlen($static);

        $this->code .= <<<CODE

            // static path
            
            \$uri{$this->nesting} = \$uri;
            
            if (strpos(\$uri, "$static") === 0) {
                \$uri = substr(\$uri, $staticLength);

            CODE;
    }

    /**
     * @inheritdoc
     */
    public function leaveStatic(StaticNode $node): void
    {
        $this->code .= <<<CODE

            }
            
            \$uri = \$uri{$this->nesting};
            
            // static path end

            CODE;

        $this->leave();
    }

    /**
     * @inheritdoc
     */
    public function enterLeaf(LeafNode $node): void
    {
        $this->enter();

        $method = $node->getRoute()->getMethod();
        $index = $node->getIndex();

        $this->code .= <<<CODE

            // method check
            
            if (\$uri === "") {
                if (\$method === "$method") {
                    return $index;
                } else {
                    \$allowed["$method"] = $index;
                }
            }
            
            // method check end

            CODE;
    }

    /**
     * @inheritdoc
     */
    public function leaveLeaf(LeafNode $node): void
    {
        $this->leave();
    }

    /**
     * Returns the string representation of the code.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->code;
    }

    /**
     * Keeps track of nesting going down the tree.
     */
    private function enter(): void
    {
        $this->nesting += 1;
    }

    /**
     * Keeps track of nesting going up in the tree.
     */
    private function leave(): void
    {
        $this->nesting -= 1;
    }
}
