<?php

declare(strict_types=1);

namespace Svoboda\Router\Types;

/**
 * Attribute types.
 */
class Types
{
    /**
     * The default attribute types and their regular expressions.
     *
     * @var string[]
     */
    private const DEFAULT_PATTERNS = [
        "any" => "[^/]+",
        "alnum" => "[a-zA-Z0-9]+",
        "alpha" => "[a-zA-Z]+",
        "date" => "\d{4}-\d{2}-\d{2}",
        "digit" => "\d",
        "number" => "\d+",
        "word" => "\w+",
    ];

    /**
     * The default implicit attribute type.
     *
     * @var string
     */
    private const DEFAULT_IMPLICIT = "any";

    /**
     * Attribute types and their regular expressions.
     *
     * @var string[]
     */
    private $patterns;

    /**
     * The implicit attribute type.
     *
     * @var string
     */
    private $implicit;

    /**
     * Constructor.
     *
     * @param string[] $patterns
     * @param string $implicit
     * @throws InvalidTypes
     */
    public function __construct(array $patterns, string $implicit)
    {
        if (empty($patterns)) {
            throw InvalidTypes::emptyPatterns();
        }

        if (!array_key_exists($implicit, $patterns)) {
            throw InvalidTypes::implicitTypeMissing($implicit);
        }

        foreach ($patterns as $name => $pattern) {
            $name = (string)$name;

            if (!preg_match("#^\w+$#", $name)) {
                throw InvalidTypes::invalidTypeName($name);
            }

            if (@preg_match("#$pattern#", "") === false) {
                throw InvalidTypes::invalidTypePattern($name, $pattern);
            }
        }

        $this->patterns = $patterns;
        $this->implicit = $implicit;
    }

    /**
     * Creates the default attribute types.
     *
     * @return Types
     * @throws InvalidTypes
     */
    public static function createDefault(): self
    {
        return new self(self::DEFAULT_PATTERNS, self::DEFAULT_IMPLICIT);
    }

    /**
     * Returns the implicit attribute type.
     *
     * @return string
     */
    public function getImplicit(): string
    {
        return $this->implicit;
    }

    /**
     * Determines if the given type exists.
     *
     * @param string $type
     * @return bool
     */
    public function contain(string $type): bool
    {
        return array_key_exists($type, $this->patterns);
    }

    /**
     * Returns the pattern for the given type.
     *
     * @param string $type
     * @return string
     */
    public function getPatternFor(string $type): string
    {
        return $this->patterns[$type];
    }
}
