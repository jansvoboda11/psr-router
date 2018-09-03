<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler\Paths;

use Svoboda\Router\Compiler\Tree\AttributeNode;
use Svoboda\Router\Compiler\Tree\LeafNode;
use Svoboda\Router\Compiler\Tree\OptionalNode;
use Svoboda\Router\Compiler\Tree\StaticNode;
use Svoboda\Router\Compiler\Tree\Tree;
use Svoboda\Router\Compiler\Tree\TreeVisitor;

class PathsCode extends TreeVisitor
{
    /**
     * The generated matcher code.
     *
     * @var string
     */
    private $code;

    /**
     * Constructor.
     *
     * @param Tree $tree
     * @param string $class
     */
    public function __construct(Tree $tree, string $class)
    {
        $this->code = <<<CODE
use Psr\Http\Message\ServerRequestInterface;
use Svoboda\Router\Matcher\AbstractMatcher;
use Svoboda\Router\Failure;
use Svoboda\Router\Match;
use Svoboda\Router\RouteCollection;

class $class extends AbstractMatcher
{
    private \$routes;

    public function __construct(RouteCollection \$routes)
    {
        \$this->routes = \$routes;
    }

    public function match(ServerRequestInterface \$request): Match
    {
        \$matches = [];
        \$allowed = [];

        \$index = \$this->matchInner(\$request, \$matches, \$allowed);

        if (\$index === null) {
            throw new Failure(\$allowed, \$request);
        }

        \$route = \$this->routes->all()[\$index];

        return \$this->createResult(\$route, \$request, \$matches);
    }

    private function matchInner(ServerRequestInterface \$request, array &\$matches, array &\$allowed): ?int
    {
        \$path = \$request->getUri()->getPath();
        \$method = \$request->getMethod();

CODE;

        $tree->accept($this);

        $this->code .= <<<CODE

        return null;
    }
}
CODE;
    }

    public function enterTree(Tree $tree): void
    {
        $this->code .= <<<CODE

\$uriArray = [];
\$matchesArray = [];

\$uri = \$path;
\$matches = [];

CODE;
    }

    public function enterAttribute(AttributeNode $node): void
    {
        $pattern = $node->getTypePattern();

        $this->code .= <<<CODE

// attribute path

\$uriArray[] = \$uri;
\$matchesArray[] = \$matches;

if (preg_match("#^($pattern)#", \$uri, \$ms) === 1) {
    \$matches[] = \$ms[1];
    \$uri = substr(\$uri, strlen(\$ms[1]));

CODE;
    }

    public function leaveAttribute(AttributeNode $node): void
    {
        $this->code .= <<<CODE

}

\$uri = array_pop(\$uriArray);
\$matches = array_pop(\$matchesArray);

// attribute path end

CODE;
    }

    public function enterOptional(OptionalNode $node): void
    {
        $this->code .= <<<CODE

// optional path

CODE;

        $node->skipToLeaves($this);
    }

    public function leaveOptional(OptionalNode $node): void
    {
        $node->skipToLeaves($this);

        $this->code .= <<<CODE

// optional path end

CODE;
    }

    public function enterStatic(StaticNode $node): void
    {
        $static = $node->getStatic();
        $staticLength = strlen($static);

        $this->code .= <<<CODE

// static path

\$uriArray[] = \$uri;

if (strpos(\$uri, "$static") === 0) {
    \$uri = substr(\$uri, $staticLength);

CODE;
    }

    public function leaveStatic(StaticNode $node): void
    {
        $this->code .= <<<CODE

}

\$uri = array_pop(\$uriArray);

// static path end

CODE;
    }

    public function enterLeaf(LeafNode $node): void
    {
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
     * Returns the string representation of the code.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->code;
    }
}
