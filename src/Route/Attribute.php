<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Route;

/**
 * The route attribute.
 */
class Attribute
{
    /**
     * Name of the attribute.
     *
     * @var string
     */
    private $name;

    /**
     * Data type of the attribute.
     *
     * @var null|string
     */
    private $type;

    /**
     * Is the attribute required?
     *
     * @var bool
     */
    private $required;

    /**
     * Constructor.
     *
     * @param string $name
     * @param null|string $type
     * @param bool $required
     */
    public function __construct(string $name, ?string $type, bool $required)
    {
        $this->name = $name;
        $this->type = $type;
        $this->required = $required;
    }

    /**
     * Makes the attribute optional (not required).
     */
    public function makeOptional(): void
    {
        $this->required = false;
    }

    /**
     * Returns the attribute name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the attribute type.
     *
     * @return null|string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Checks whether the attribute is required.
     *
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }
}
