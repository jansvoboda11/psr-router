<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Parser\Parts;

use Svoboda\PsrRouter\Compiler\PartsVisitor;

/**
 * Main part of the route consisting of static part, attributes and the next route part (usually main or optional).
 */
class MainPart implements RoutePart
{
    /**
     * @var StaticPart
     */
    private $static;

    /**
     * @var AttributePart[]
     */
    private $attributes;

    /**
     * @var RoutePart
     */
    private $next;

    /**
     * @param StaticPart $static
     * @param AttributePart[] $attributes
     * @param RoutePart $next
     */
    public function __construct(StaticPart $static, array $attributes, RoutePart $next)
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
