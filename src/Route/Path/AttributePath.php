<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Route\Path;

use Svoboda\PsrRouter\Compiler\PartsVisitor;

/**
 * Route part that represents user-defined attribute.
 */
class AttributePath implements RoutePath
{
    /**
     * The attribute name.
     *
     * @var string
     */
    private $name;

    /**
     * The attribute type.
     *
     * @var null|string
     */
    private $type;

    /**
     * @param string $name
     * @param null|string $type
     */
    public function __construct(string $name, ?string $type = null)
    {
        $this->name = $name;
        $this->type = $type;
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
     * @inheritdoc
     */
    public function getDefinition(): string
    {
        $name = $this->name;
        $type = $this->type;

        if (is_null($type)) {
            return "{" . $name . "}";
        }

        return "{" . $name . ":" . $type . "}";
    }

    /**
     * @inheritdoc
     */
    public function getAttributes(): array
    {
        return [
            [
                "name" => $this->name,
                "type" => $this->type,
                "required" => true,
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function accept(PartsVisitor $visitor): void
    {
        $visitor->enterAttribute($this);

        $visitor->leaveAttribute($this);
    }
}
