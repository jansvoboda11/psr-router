<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Route\Path;

use Svoboda\PsrRouter\Compiler\PartsVisitor;

/**
 * The null object for route part.
 */
class EmptyPath implements RoutePath
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
