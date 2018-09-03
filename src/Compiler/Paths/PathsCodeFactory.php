<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler\Paths;

use Svoboda\Router\Compiler\Tree\Tree;

class PathsCodeFactory
{
    public function create(Tree $tree, string $class): string
    {
        $pathsCode = new PathsCode($tree, $class);

        return (string)$pathsCode;
    }
}
