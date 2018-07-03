<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Compiler;

use Svoboda\PsrRouter\Route\Path\AttributePath;
use Svoboda\PsrRouter\Route\Path\OptionalPath;
use Svoboda\PsrRouter\Route\Path\PathVisitor;
use Svoboda\PsrRouter\Route\Path\RoutePath;
use Svoboda\PsrRouter\Route\Path\StaticPath;

/**
 * Builds regular expression for route path.
 */
class PatternBuilder extends PathVisitor
{
    /**
     * Regular expressions for attribute types.
     *
     * @var string[]
     */
    private $typePatterns;

    /**
     * The implicit attribute type.
     *
     * @var string
     */
    private $implicitType;

    /**
     * The built regular expression.
     *
     * @var string
     */
    private $pattern;

    /**
     * Creates regular expression for the route part.
     *
     * @param RoutePath $path
     * @param Context $context
     * @return string
     */
    public function buildPattern(RoutePath $path, Context $context)
    {
        $this->pattern = "";
        $this->typePatterns = $context->getTypePatterns();
        $this->implicitType = $context->getImplicitType();

        $path->accept($this);

        return $this->pattern;
    }

    /**
     * @inheritdoc
     */
    public function enterAttribute(AttributePath $path): void
    {
        $name = $path->getName();
        $type = $path->getType() ?? $this->implicitType;

        $typePattern = $this->typePatterns[$type];

        $this->pattern .= "(?'$name'$typePattern)";
    }

    /**
     * @inheritdoc
     */
    public function enterOptional(OptionalPath $path): void
    {
        $this->pattern .= "(?:";
    }

    /**
     * @inheritdoc
     */
    public function leaveOptional(OptionalPath $path): void
    {
        $this->pattern .= ")?";
    }

    /**
     * @inheritdoc
     */
    public function enterStatic(StaticPath $path): void
    {
        $this->pattern .= $path->getStatic();
    }
}
