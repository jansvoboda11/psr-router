<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Route\Path;

/**
 * Two-pass visitor of all types of route path.
 */
abstract class PathVisitor
{
    /**
     * Enters the attribute path.
     *
     * @param AttributePath $path
     */
    public function enterAttribute(AttributePath $path): void
    {
        //
    }

    /**
     * Leaves the attribute path.
     *
     * @param AttributePath $path
     */
    public function leaveAttribute(AttributePath $path): void
    {
        //
    }

    /**
     * Enters the optional path.
     *
     * @param OptionalPath $path
     */
    public function enterOptional(OptionalPath $path): void
    {
        //
    }

    /**
     * Leaves the optional path.
     *
     * @param OptionalPath $path
     */
    public function leaveOptional(OptionalPath $path): void
    {
        //
    }

    /**
     * Enters the static path.
     *
     * @param StaticPath $path
     */
    public function enterStatic(StaticPath $path): void
    {
        //
    }

    /**
     * Leaves the static path.
     *
     * @param StaticPath $path
     */
    public function leaveStatic(StaticPath $path): void
    {
        //
    }
}
