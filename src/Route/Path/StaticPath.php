<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Route\Path;

use Svoboda\PsrRouter\Compiler\PartsVisitor;

/**
 * Static part of the route.
 */
class StaticPath implements RoutePath
{
    /**
     * The static string in route.
     *
     * @var string
     */
    private $static;

    /**
     * The next part of the route.
     *
     * @var RoutePath
     */
    private $next;

    /**
     * @param string $static
     * @param null|RoutePath $next
     */
    public function __construct(string $static, ?RoutePath $next = null)
    {
        $this->static = $static;
        $this->next = $next ?? new EmptyPath();
    }

    /**
     * Returns the static string.
     *
     * @return string
     */
    public function getStatic(): string
    {
        return $this->static;
    }

    /**
     * @inheritdoc
     */
    public function getDefinition(): string
    {
        $nextDefinition = $this->next->getDefinition();

        return $this->static . $nextDefinition;
    }

    /**
     * @inheritdoc
     */
    public function getAttributes(): array
    {
        return $this->next->getAttributes();
    }

    /**
     * @inheritdoc
     */
    public function accept(PartsVisitor $visitor): void
    {
        $visitor->enterStatic($this);

        $this->next->accept($visitor);

        $visitor->leaveStatic($this);
    }
}
