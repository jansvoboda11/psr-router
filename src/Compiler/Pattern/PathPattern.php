<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler\Pattern;

use Svoboda\Router\Route\Path\AttributePath;
use Svoboda\Router\Route\Path\OptionalPath;
use Svoboda\Router\Route\Path\PathVisitor;
use Svoboda\Router\Route\Path\RoutePath;
use Svoboda\Router\Route\Path\StaticPath;

/**
 * The regular expression of a path.
 */
class PathPattern extends PathVisitor
{
    /**
     * The regular expression.
     *
     * @var string
     */
    private $pattern;

    /**
     * Constructor.
     *
     * @param RoutePath $path
     */
    public function __construct(RoutePath $path)
    {
        $this->pattern = "";

        $path->accept($this);
    }

    /**
     * Converts the object into a string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->pattern;
    }

    /**
     * Creates a regular expression for the attribute.
     *
     * @param AttributePath $path
     */
    public function enterAttribute(AttributePath $path): void
    {
        $pattern = $path->getTypePattern();

        $this->pattern .= "($pattern)";
    }

    /**
     * Creates the start of regular expression for the optional part of the
     * path.
     *
     * @param OptionalPath $path
     */
    public function enterOptional(OptionalPath $path): void
    {
        $this->pattern .= "(?:";
    }

    /**
     * Creates the end of regular expression for the optional part of the path.
     *
     * @param OptionalPath $path
     */
    public function leaveOptional(OptionalPath $path): void
    {
        $this->pattern .= ")?";
    }

    /**
     * Creates the regular expression for the static part of the path.
     *
     * @param StaticPath $path
     */
    public function enterStatic(StaticPath $path): void
    {
        $this->pattern .= $path->getStatic();
    }
}
