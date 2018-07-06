<?php

declare(strict_types=1);

namespace Svoboda\Router\Route\Path;

/**
 * Two-pass visitor of all types of route path.
 */
abstract class PathVisitor
{
    /**
     * Enters the attribute path.
     *
     * @param AttributePath $path
     * @param mixed $data
     */
    public function enterAttribute(AttributePath $path, &$data): void
    {
        //
    }

    /**
     * Leaves the attribute path.
     *
     * @param AttributePath $path
     * @param mixed $data
     */
    public function leaveAttribute(AttributePath $path, &$data): void
    {
        //
    }

    /**
     * Enters the optional path.
     *
     * @param OptionalPath $path
     * @param mixed $data
     */
    public function enterOptional(OptionalPath $path, &$data): void
    {
        //
    }

    /**
     * Leaves the optional path.
     *
     * @param OptionalPath $path
     * @param mixed $data
     */
    public function leaveOptional(OptionalPath $path, &$data): void
    {
        //
    }

    /**
     * Enters the static path.
     *
     * @param StaticPath $path
     * @param mixed $data
     */
    public function enterStatic(StaticPath $path, &$data): void
    {
        //
    }

    /**
     * Leaves the static path.
     *
     * @param StaticPath $path
     * @param mixed $data
     */
    public function leaveStatic(StaticPath $path, &$data): void
    {
        //
    }
}
