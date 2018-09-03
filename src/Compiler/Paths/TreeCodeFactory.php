<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler\Paths;

use Svoboda\Router\Compiler\Tree\Tree;

/**
 * Creates new tree PHP code.
 */
class TreeCodeFactory
{
    /**
     * Creates new tree PHP code.
     *
     * @param Tree $tree
     * @param string $class
     * @return string
     */
    public function create(Tree $tree, string $class): string
    {
        $pathsCode = new TreeCode($tree, $class);

        return (string)$pathsCode;
    }
}
