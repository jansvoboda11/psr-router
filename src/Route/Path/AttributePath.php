<?php

declare(strict_types=1);

namespace Svoboda\Router\Route\Path;

use Svoboda\Router\Route\Attribute;
use Svoboda\Router\Types\TypeCollection;

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
     * The attribute types.
     *
     * @var TypeCollection
     */
    private $types;

    /**
     * The next part of the route.
     *
     * @var RoutePath
     */
    private $next;

    /**
     * Constructor.
     *
     * @param string $name
     * @param null|string $type
     * @param TypeCollection $types
     * @param null|RoutePath $next
     */
    public function __construct(string $name, ?string $type, TypeCollection $types, ?RoutePath $next = null)
    {
        $this->name = $name;
        $this->type = $type;
        $this->types = $types;
        $this->next = $next ?? new EmptyPath();
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
     * @return string
     */
    public function getType(): string
    {
        return $this->type ?? $this->types->getImplicit()->getName();
    }

    /**
     * Returns the attribute pattern.
     *
     * @return string
     */
    public function getPattern(): string
    {
        $type = $this->getType();

        return $this->types->getPatternFor($type);
    }

    /**
     * @inheritdoc
     */
    public function getDefinition(): string
    {
        $name = $this->name;
        $type = $this->type;

        $typeDefinition = ($type === null) ? "" : ":$type";

        $nextDefinition = $this->next->getDefinition();

        return "{" . $name . $typeDefinition . "}" . $nextDefinition;
    }

    /**
     * @inheritdoc
     */
    public function getAttributes(): array
    {
        $attribute = new Attribute($this->getName(), $this->getType(), true);

        $nextAttributes = $this->next->getAttributes();

        return array_merge([$attribute], $nextAttributes);
    }

    /**
     * @inheritdoc
     */
    public function accept(PathVisitor $visitor): void
    {
        $visitor->enterAttribute($this);

        $this->next->accept($visitor);

        $visitor->leaveAttribute($this);
    }
}
