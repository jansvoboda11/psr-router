<?php

declare(strict_types=1);

namespace Svoboda\Router\Route\Path;

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
     * Constructor.
     *
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
    public function accept(PathVisitor $visitor, &$data): void
    {
        $visitor->enterStatic($this, $data);

        $this->next->accept($visitor, $data);

        $visitor->leaveStatic($this, $data);
    }
}
