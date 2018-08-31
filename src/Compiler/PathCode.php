<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler;

use Svoboda\Router\Route\Path\AttributePath;
use Svoboda\Router\Route\Path\EmptyPath;
use Svoboda\Router\Route\Path\OptionalPath;
use Svoboda\Router\Route\Path\PathVisitor;
use Svoboda\Router\Route\Path\StaticPath;
use Svoboda\Router\Route\Route;

class PathCode extends PathVisitor
{
    /**
     * The route.
     *
     * @var Route
     */
    private $route;

    /**
     * Index of the route in route collection.
     *
     * @var int
     */
    private $index;

    /**
     * The generated matcher code.
     *
     * @var string
     */
    private $code;

    /**
     * Constructor.
     *
     * @param Route $route
     * @param int $index
     */
    public function __construct(Route $route, int $index)
    {
        $this->route = $route;
        $this->index = $index;

        $this->code = <<<'CODE'
        
// route

$uri = $path;
$matches = [];
CODE;

        $route->getPath()->accept($this);
    }

    public function enterAttribute(AttributePath $path): void
    {
        $pattern = $path->getTypePattern();

        $this->code .= <<<CODE

// attribute path

if (preg_match("#^($pattern)#", \$uri, \$ms) === 1) {
    \$matches[] = \$ms[1];
    \$uri = substr(\$uri, strlen(\$ms[1]));
CODE;
    }

    public function leaveAttribute(AttributePath $path): void
    {
        $this->code .= <<<'CODE'
}

// attribute path end

CODE;
    }

    public function enterOptional(OptionalPath $path): void
    {
        $methodCode = $this->generateMethodCheck();

        $this->code .= <<<CODE
        
// optional path

    $methodCode
CODE;
    }

    public function leaveOptional(OptionalPath $path): void
    {
        $methodCode = $this->generateMethodCheck();

        $this->code .= <<<CODE
        
// optional path

    $methodCode
CODE;
    }

    public function enterStatic(StaticPath $path): void
    {
        $static = $path->getStatic();
        $staticLength = strlen($static);

        $this->code .= <<<CODE
        
// static path

\$prefix = substr(\$uri, 0, $staticLength);
if (\$prefix === "$static") {
    \$uri = substr(\$uri, $staticLength);
CODE;
    }

    public function leaveStatic(StaticPath $path): void
    {
        $this->code .= <<<'CODE'
}

// static path end

CODE;
    }

    public function enterEmpty(EmptyPath $path): void
    {
        $this->code .= $this->generateMethodCheck();
    }

    public function generateMethodCheck(): string
    {
        $method = $this->route->getMethod();
        $index = $this->index;

        return <<<CODE
if (\$uri === ""){
    if (\$method === "$method") {
        return $index;
    } else {
        \$allowed["$method"] = $index;
    }
}
CODE;
    }

    public function __toString(): string
    {
        return $this->code;
    }
}
