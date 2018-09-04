<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler\Pattern;

use Svoboda\Router\Compiler\Tree\Tree;

class TreePatternFactory
{
    public function create(Tree $tree): string
    {
        $pattern = new TreePattern($tree);

        return (string)$pattern;
    }
}
