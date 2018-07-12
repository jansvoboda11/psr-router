<?php

declare(strict_types=1);

namespace Svoboda\Router\Route\Path;

use Svoboda\Router\Route\Attribute;

/**
 * Wrapper for the optional part of the route path.
 */
class OptionalPath implements RoutePath
{
    /**
     * The optional part of route path.
     *
     * @var RoutePath
     */
    private $optional;

    /**
     * Constructor.
     *
     * @param RoutePath $optional
     */
    public function __construct(RoutePath $optional)
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

        return array_map(function (Attribute $attribute) {
            $attribute->makeOptional();

            return $attribute;
        }, $attributes);
    }

    /**
     * @inheritdoc
     */
    public function accept(PathVisitor $visitor): void
    {
        $visitor->enterOptional($this);

        $this->optional->accept($visitor);

        $visitor->leaveOptional($this);
    }
}
