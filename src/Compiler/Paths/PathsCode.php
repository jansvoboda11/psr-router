<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler\Paths;

use Svoboda\Router\Compiler\Tree\Tree;
use Svoboda\Router\Compiler\Tree\TreeVisitor;

class PathsCode extends TreeVisitor
{
    /**
     * PHP code.
     *
     * @var string
     */
    private $code;

    public function __construct(Tree $tree, string $class)
    {
        // todo: implement

        $this->code = "class $class implements Matcher {}";

        $tree->accept($this);
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
}
