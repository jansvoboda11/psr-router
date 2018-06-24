<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Parser\Parts;

use Svoboda\PsrRouter\Compiler\PartsVisitor;
use Svoboda\PsrRouter\InvalidRoute;
use function array_intersect_key;
use function array_merge;

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
            $attributes = $this->mergeAttributes($attributes, $attribute->getAttributes());
        }

        return $this->mergeAttributes($attributes, $this->next->getAttributes());
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

    /**
     * Merges two sets of attributes. Fails if both contain the same attribute.
     *
     * @param array $first
     * @param array $second
     * @return array
     * @throws InvalidRoute
     */
    private function mergeAttributes(array $first, array $second): array
    {
        if (array_intersect_key($first, $second) !== []) {
            throw new InvalidRoute();
        }

        return array_merge($first, $second);
    }
}
