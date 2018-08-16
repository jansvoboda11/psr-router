<?php

declare(strict_types=1);

namespace Svoboda\Router\Route\Path;

use Svoboda\Router\Route\Attribute;
use Svoboda\Router\Types\Type;

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
     * @var Type
     */
    private $type;

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
     * @param Type $type
     * @param null|RoutePath $next
     */
    public function __construct(string $name, Type $type, ?RoutePath $next = null)
    {
        $this->name = $name;
        $this->type = $type;
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
     * Returns the pattern of the attribute type.
     *
     * @return string
     */
    public function getTypePattern(): string
    {
        return $this->type->getPattern();
    }

    /**
     * @inheritdoc
     */
    public function getDefinition(): string
    {
        $name = $this->name;
        $type = $this->type->getName();

        $typeDefinition = ($type === "") ? "" : ":$type";

        $nextDefinition = $this->next->getDefinition();

        return "{" . $name . $typeDefinition . "}" . $nextDefinition;
    }

    /**
     * @inheritdoc
     */
    public function getAttributes(): array
    {
        $attribute = new Attribute($this->name, $this->type, true);

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
