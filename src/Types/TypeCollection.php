<?php

declare(strict_types=1);

namespace Svoboda\Router\Types;

/**
 * Attribute types.
 */
class TypeCollection
{
    /**
     * Attribute types.
     *
     * @var Type[]
     */
    private $types;

    /**
     * Name of the implicit type.
     *
     * @var string
     */
    private $implicit;

    /**
     * Constructor.
     *
     * @param Type[] $types
     * @throws InvalidTypes
     */
    public function __construct(array $types)
    {
        if (empty($types)) {
            throw InvalidTypes::emptyPatterns();
        }

        $typesMap = [];

        foreach ($types as $type) {
            $typesMap[$type->getName()] = $type;
        }

        $this->types = $typesMap;
        $this->implicit = $types[0]->getName();
    }

    /**
     * Creates the default attribute types.
     *
     * @return TypeCollection
     * @throws InvalidTypes
     */
    public static function createDefault(): self
    {
        return new self([
            new Type("any", "[^/]+"),
            new Type("alnum", "[a-zA-Z0-9]+"),
            new Type("alpha", "[a-zA-Z]+"),
            new Type("date", "\d{4}-\d{2}-\d{2}"),
            new Type("digit", "\d"),
            new Type("number", "\d+"),
            new Type("word", "\w+"),
        ]);
    }

    /**
     * Returns the implicit attribute type.
     *
     * @return Type
     */
    public function getImplicit(): Type
    {
        return $this->types[$this->implicit];
    }

    /**
     * Determines if the given type exists.
     *
     * @param string $name
     * @return bool
     */
    public function hasNamed(string $name): bool
    {
        return array_key_exists($name, $this->types);
    }

    /**
     * Returns the pattern for the given type.
     *
     * @param string $name
     * @return string
     */
    public function getPatternFor(string $name): string
    {
        return $this->types[$name]->getPattern();
    }
}
