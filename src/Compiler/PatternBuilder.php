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
     * The type context.
     *
     * @var Context
     */
    private $context;

    /**
     * Constructor.
     *
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * Creates regular expression for the route part.
     *
     * @param RoutePath $path
     * @return string
     */
    public function buildPattern(RoutePath $path)
    {
        $pattern = "";

        $path->accept($this, $pattern);

        return $pattern;
    }

    /**
     * @inheritdoc
     */
    public function enterAttribute(AttributePath $path, &$pattern = null): void
    {
        $name = $path->getName();
        $type = $path->getType() ?? $this->context->getImplicitType();

        $typePatterns = $this->context->getTypePatterns();
        $typePattern = $typePatterns[$type];

        $pattern .= "(?'$name'$typePattern)";
    }

    /**
     * @inheritdoc
     */
    public function enterOptional(OptionalPath $path, &$pattern = null): void
    {
        $pattern .= "(?:";
    }

    /**
     * @inheritdoc
     */
    public function leaveOptional(OptionalPath $path, &$pattern = null): void
    {
        $pattern .= ")?";
    }

    /**
     * @inheritdoc
     */
    public function enterStatic(StaticPath $path, &$pattern = null): void
    {
        $pattern .= $path->getStatic();
    }
}
