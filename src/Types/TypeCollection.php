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
     * Constructor.
     *
     * @param Type[] $types
     * @throws InvalidTypes
     */
    public function __construct(array $types)
    {
        if (empty($types)) {
            throw InvalidTypes::emptyCollection();
        }

        $this->types = [
            "" => $types[0]->createImplicit(),
        ];

        foreach ($types as $type) {
            $this->types[$type->getName()] = $type;
        }
    }

    /**
     * Creates a collection with the default types.
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
     * Determines if the collection has a type with given name.
     *
     * @param string $name
     * @return bool
     */
    public function hasNamed(string $name): bool
    {
        return array_key_exists($name, $this->types);
    }

    /**
     * Returns type with the given name.
     *
     * @param string $name
     * @return Type
     */
    public function getNamed(string $name): Type
    {
        return $this->types[$name];
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
