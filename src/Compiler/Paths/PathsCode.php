<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler\Paths;

use Svoboda\Router\Compiler\Tree\Tree;
use Svoboda\Router\Compiler\Tree\TreeVisitor;

class PathsCode extends TreeVisitor
{
    public function __construct(Tree $tree, string $class)
    {
        // todo: implement

        $tree->accept($this);
    }
}
