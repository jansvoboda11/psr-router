<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler;

use Svoboda\Router\Route\Path\AttributePath;
use Svoboda\Router\Route\Path\OptionalPath;
use Svoboda\Router\Route\Path\PathVisitor;
use Svoboda\Router\Route\Path\RoutePath;
use Svoboda\Router\Route\Path\StaticPath;

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
     * @throws CompilationFailure
     */
    public function buildPattern(RoutePath $path): string
    {
        $pattern = "";

        $path->accept($this, $pattern);

        return $pattern;
    }

    /**
     * Creates a regular expression for the attribute.
     *
     * @param AttributePath $path
     * @param mixed $pattern
     * @throws CompilationFailure
     */
    public function enterAttribute(AttributePath $path, &$pattern): void
    {
        $name = $path->getName();
        $type = $path->getType() ?? $this->context->getImplicitType();

        $typePatterns = $this->context->getTypePatterns();

        if (!key_exists($type, $typePatterns)) {
            throw CompilationFailure::unknownType($name, $type);
        }

        $typePattern = $typePatterns[$type];

        $pattern .= "(?'$name'$typePattern)";
    }

    /**
     * Creates the start of regular expression for the optional part of the
     * path.
     *
     * @param OptionalPath $path
     * @param mixed $pattern
     */
    public function enterOptional(OptionalPath $path, &$pattern): void
    {
        $pattern .= "(?:";
    }

    /**
     * Creates the end of regular expression for the optional part of the path.
     *
     * @param OptionalPath $path
     * @param mixed $pattern
     */
    public function leaveOptional(OptionalPath $path, &$pattern): void
    {
        $pattern .= ")?";
    }

    /**
     * Creates the regular expression for the static part of the path.
     *
     * @param StaticPath $path
     * @param mixed $pattern
     */
    public function enterStatic(StaticPath $path, &$pattern): void
    {
        $pattern .= $path->getStatic();
    }
}
