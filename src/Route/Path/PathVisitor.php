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
     * @param null|mixed $data
     */
    public function enterAttribute(AttributePath $path, &$data = null): void
    {
        //
    }

    /**
     * Leaves the attribute path.
     *
     * @param AttributePath $path
     * @param null|mixed $data
     */
    public function leaveAttribute(AttributePath $path, &$data = null): void
    {
        //
    }

    /**
     * Enters the optional path.
     *
     * @param OptionalPath $path
     * @param null|mixed $data
     */
    public function enterOptional(OptionalPath $path, &$data = null): void
    {
        //
    }

    /**
     * Leaves the optional path.
     *
     * @param OptionalPath $path
     * @param null|mixed $data
     */
    public function leaveOptional(OptionalPath $path, &$data = null): void
    {
        //
    }

    /**
     * Enters the static path.
     *
     * @param StaticPath $path
     * @param null|mixed $data
     */
    public function enterStatic(StaticPath $path, &$data = null): void
    {
        //
    }

    /**
     * Leaves the static path.
     *
     * @param StaticPath $path
     * @param null|mixed $data
     */
    public function leaveStatic(StaticPath $path, &$data = null): void
    {
        //
    }
}
