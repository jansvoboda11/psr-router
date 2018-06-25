<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Route\Parts;

use Svoboda\PsrRouter\Compiler\PartsVisitor;

/**
 * Wrapper for the optional part of the route.
 */
class OptionalPart implements RoutePart
{
    /**
     * @var RoutePart
     */
    private $optional;

    /**
     * @param RoutePart $optional
     */
    public function __construct(RoutePart $optional)
    {
        $this->optional = $optional;
    }

    /**
     * @inheritdoc
     */
    public function getDefinition(): string
    {
        $optionalDefinition = $this->optional->getDefinition();

        return "[" . $optionalDefinition . "]";
    }

    /**
     * @inheritdoc
     */
    public function getAttributes(): array
    {
        $attributes = $this->optional->getAttributes();

        return array_map(function ($attribute) {
            $attribute["required"] = false;

            return $attribute;
        }, $attributes);
    }

    /**
     * @inheritdoc
     */
    public function accept(PartsVisitor $visitor): void
    {
        $visitor->enterOptional($this);

        $this->optional->accept($visitor);

        $visitor->leaveOptional($this);
    }
}
