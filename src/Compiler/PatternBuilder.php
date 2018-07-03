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
     * Creates regular expression for the route part.
     *
     * @param RoutePath $path
     * @param Context $context
     * @return string
     */
    public function buildPattern(RoutePath $path, Context $context)
    {
        $data = [
            "pattern" => "",
            "typePatterns" => $context->getTypePatterns(),
            "implicitType" => $context->getImplicitType(),
        ];

        $path->accept($this, $data);

        return $data["pattern"];
    }

    /**
     * @inheritdoc
     */
    public function enterAttribute(AttributePath $path, &$data = null): void
    {
        $name = $path->getName();
        $type = $path->getType() ?? $data["implicitType"];

        $typePattern = $data["typePatterns"][$type];

        $data["pattern"] .= "(?'$name'$typePattern)";
    }

    /**
     * @inheritdoc
     */
    public function enterOptional(OptionalPath $path, &$data = null): void
    {
        $data["pattern"] .= "(?:";
    }

    /**
     * @inheritdoc
     */
    public function leaveOptional(OptionalPath $path, &$data = null): void
    {
        $data["pattern"] .= ")?";
    }

    /**
     * @inheritdoc
     */
    public function enterStatic(StaticPath $path, &$data = null): void
    {
        $data["pattern"] .= $path->getStatic();
    }
}
