<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Route\Parts;

use Svoboda\PsrRouter\Compiler\PartsVisitor;

/**
 * The null object for route part.
 */
class EmptyPart implements RoutePart
{
    /**
     * @inheritdoc
     */
    public function getDefinition(): string
    {
        return "";
    }

    /**
     * @inheritdoc
     */
    public function getAttributes(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function accept(PartsVisitor $visitor): void
    {
        //
    }
}
