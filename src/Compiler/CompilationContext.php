<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Compiler;

class CompilationContext
{
    /**
     * @var string[]
     */
    private const DEFAULT_TYPE_PATTERNS = [
        "any" => "[^/]+",
        "num" => "\d+",
    ];

    /**
     * @var string
     */
    private const DEFAULT_IMPLICIT_TYPE = "any";

    /**
     * @var string[]
     */
    private $typePatterns;

    /**
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
     * Creates a default compilation context.
     *
     * @return self
     */
    public static function createDefault(): self
    {
        return new self(self::DEFAULT_TYPE_PATTERNS, self::DEFAULT_IMPLICIT_TYPE);
    }

    /**
     * Returns patterns of all attribute types.
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
