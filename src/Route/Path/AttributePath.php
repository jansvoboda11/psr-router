<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Route\Path;

use Svoboda\PsrRouter\Route\Attribute;

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
     * @param null|RoutePath $next
     */
    public function __construct(string $name, ?string $type, ?RoutePath $next = null)
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

        $typeDefinition = is_null($type) ? "" : ":$type";

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
