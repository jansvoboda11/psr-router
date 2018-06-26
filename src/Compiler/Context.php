<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Compiler;

/**
 * Provides the context for compilation: available types and their regular
 * expressions.
 */
class Context
{
    /**
     * The default attribute types and their regular expressions.
     *
     * @var string[]
     */
    private const DEFAULT_TYPE_PATTERNS = [
        "any" => "[^/]+",
        "num" => "\d+",
    ];

    /**
     * The default implicit attribute type.
     *
     * @var string
     */
    private const DEFAULT_IMPLICIT_TYPE = "any";

    /**
     * Attribute types and their regular expressions.
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
     * @param string[] $typePatterns
     * @param string $defaultType
     */
    public function __construct(array $typePatterns, string $defaultType)
    {
        $this->typePatterns = $typePatterns;
        $this->implicitType = $defaultType;
    }

    /**
     * Creates the default context.
     *
     * @return self
     */
    public static function createDefault(): self
    {
        return new self(self::DEFAULT_TYPE_PATTERNS, self::DEFAULT_IMPLICIT_TYPE);
    }

    /**
     * Returns regular expressions of all attribute types.
     *
     * @return string[]
     */
    public function getTypePatterns(): array
    {
        return $this->typePatterns;
    }

    /**
     * Returns the default attribute type.
     *
     * @return string
     */
    public function getImplicitType(): string
    {
        return $this->implicitType;
    }
}