<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Compiler;

use Svoboda\PsrRouter\Parser\Parts\AttributePart;
use Svoboda\PsrRouter\Parser\Parts\OptionalPart;
use Svoboda\PsrRouter\Parser\Parts\RoutePart;
use Svoboda\PsrRouter\Parser\Parts\StaticPart;

/**
 * Creates a single naive regular expression for a route.
 */
class NaiveRegexVisitor extends PartsVisitor
{
    /**
     * Attribute type patterns.
     *
     * @var string[]
     */
    private $typePatterns = [
        "any" => "[^/]+",
        "num" => "\d+",
    ];

    /**
     * The implicit attribute type.
     *
     * @var string
     */
    private $implicitType = "any";

    /**
     * @var string
     */
    private $regex;

    /**
     * Creates regular expression for the route part.
     *
     * @param RoutePart $part
     * @param CompilationContext $context
     * @return string
     */
    public function createRegex(RoutePart $part, CompilationContext $context)
    {
        $this->regex = "";

        $this->typePatterns = $context->getTypePatterns();
        $this->implicitType = $context->getImplicitType();

        $part->accept($this);

        return $this->regex;
    }

    /**
     * @inheritdoc
     */
    public function enterAttribute(AttributePart $part): void
    {
        $name = $part->getName();
        $type = $part->getType() ?? $this->implicitType;

        $typeRegex = $this->typePatterns[$type];

        $this->regex .= "(?'$name'$typeRegex)";
    }

    /**
     * @inheritdoc
     */
    public function enterOptional(OptionalPart $part): void
    {
        $this->regex .= "(?:";
    }

    /**
     * @inheritdoc
     */
    public function leaveOptional(OptionalPart $part): void
    {
        $this->regex .= ")?";
    }

    /**
     * @inheritdoc
     */
    public function enterStatic(StaticPart $part): void
    {
        $this->regex .= $part->getStatic();
    }
}
