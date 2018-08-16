<?php

declare(strict_types=1);

namespace Svoboda\Router\Types;

class Type
{
    /**
     * The name.
     *
     * @var string
     */
    private $name;

    /**
     * The type pattern.
     *
     * @var string
     */
    private $pattern;

    /**
     * Is the type an implicit one?
     * The default when user omits the type in route definition.
     *
     * @var bool
     */
    private $implicit;

    /**
     * Constructor.
     *
     * @param string $name
     * @param string $pattern
     * @param bool $implicit
     * @throws InvalidTypes
     */
    public function __construct(string $name, string $pattern, bool $implicit = false)
    {
        if (!preg_match("#^\w+$#", $name)) {
            throw InvalidTypes::invalidTypeName($name);
        }

        if (@preg_match("#$pattern#", "") === false) {
            throw InvalidTypes::invalidTypePattern($name, $pattern);
        }
        
        $this->name = $name;
        $this->pattern = $pattern;
        $this->implicit = $implicit;
    }

    /**
     * Returns the type name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->implicit ? "" : $this->name;
    }

    /**
     * Returns the type pattern.
     *
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * Creates the same type, but marked as implicit.
     *
     * @return Type
     * @throws InvalidTypes
     */
    public function createImplicit(): Type
    {
        return new self($this->name, $this->pattern, true);
    }
}
