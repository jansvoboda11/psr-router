<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler;

use Svoboda\Router\Route\Path\AttributePath;
use Svoboda\Router\Route\Path\OptionalPath;
use Svoboda\Router\Route\Path\PathVisitor;
use Svoboda\Router\Route\Path\RoutePath;
use Svoboda\Router\Route\Path\StaticPath;
use Svoboda\Router\Types\Types;

/**
 * Builds regular expression for route path.
 */
class PatternBuilder extends PathVisitor
{
    /**
     * Creates regular expression for the route part.
     *
     * @param RoutePath $path
     * @param Types $types
     * @return string
     */
    public function buildPattern(RoutePath $path, Types $types): string
    {
        $data = [
            "pattern" => "",
            "implicitType" => $types->getImplicit(),
            "typePatterns" => $types->getPatterns(),
        ];

        $path->accept($this, $data);

        return $data["pattern"];
    }

    /**
     * Creates a regular expression for the attribute.
     *
     * @param AttributePath $path
     * @param mixed $data
     */
    public function enterAttribute(AttributePath $path, &$data): void
    {
        $name = $path->getName();
        $type = $path->getType() ?? $data["implicitType"];

        $typePatterns = $data["typePatterns"];

        $typePattern = $typePatterns[$type];

        $data["pattern"] .= "(?'$name'$typePattern)";
    }

    /**
     * Creates the start of regular expression for the optional part of the
     * path.
     *
     * @param OptionalPath $path
     * @param mixed $data
     */
    public function enterOptional(OptionalPath $path, &$data): void
    {
        $data["pattern"] .= "(?:";
    }

    /**
     * Creates the end of regular expression for the optional part of the path.
     *
     * @param OptionalPath $path
     * @param mixed $data
     */
    public function leaveOptional(OptionalPath $path, &$data): void
    {
        $data["pattern"] .= ")?";
    }

    /**
     * Creates the regular expression for the static part of the path.
     *
     * @param StaticPath $path
     * @param mixed $data
     */
    public function enterStatic(StaticPath $path, &$data): void
    {
        $data["pattern"] .= $path->getStatic();
    }
}
