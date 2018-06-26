<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Route\Path;

use Svoboda\PsrRouter\Compiler\PartsVisitor;

/**
 * Main part of the route consisting of static part, attributes and the next
 * route part (usually main or optional).
 */
class MainPath implements RoutePath
{
    /**
     * The static part of the path.
     *
     * @var StaticPath
     */
    private $static;

    /**
     * Path attributes.
     *
     * @var AttributePath[]
     */
    private $attributes;

    /**
     * The next part of route path.
     *
     * @var RoutePath
     */
    private $next;

    /**
     * @param StaticPath $static
     * @param AttributePath[] $attributes
     * @param RoutePath $next
     */
    public function __construct(StaticPath $static, array $attributes, RoutePath $next)
    {
        $this->static = $static;
        $this->attributes = $attributes;
        $this->next = $next;
    }

    /**
     * @inheritdoc
     */
    public function getDefinition(): string
    {
        $attributesDefinition = "";

        foreach ($this->attributes as $attribute) {
            $attributesDefinition .= $attribute->getDefinition();
        }

        return $this->static->getDefinition() . $attributesDefinition . $this->next->getDefinition();
    }

    /**
     * @inheritdoc
     */
    public function getAttributes(): array
    {
        $attributes = [];

        foreach ($this->attributes as $attribute) {
            $attributes = array_merge($attributes, $attribute->getAttributes());
        }

        return array_merge($attributes, $this->next->getAttributes());
    }

    /**
     * @inheritdoc
     */
    public function accept(PartsVisitor $visitor): void
    {
        $visitor->enterMain($this);

        $this->static->accept($visitor);

        foreach ($this->attributes as $attribute) {
            $attribute->accept($visitor);
        }

        $this->next->accept($visitor);

        $visitor->leaveMain($this);
    }
}
