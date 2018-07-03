<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Route\Path;

use Svoboda\PsrRouter\Route\Attribute;

/**
 * Part of the route definition.
 */
interface RoutePath
{
    /**
     * Builds the original route definition.
     *
     * @return string
     */
    public function getDefinition(): string;

    /**
     * Aggregates all route attributes.
     *
     * @return Attribute[]
     */
    public function getAttributes(): array;

    /**
     * Accepts the visitor and allows it to visit the children nodes.
     *
     * @param PathVisitor $visitor
     */
    public function accept(PathVisitor $visitor): void;
}
