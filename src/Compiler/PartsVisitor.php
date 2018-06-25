<?php

declare(strict_types=1);

namespace Svoboda\PsrRouter\Compiler ;

use Svoboda\PsrRouter\Route\Parts\AttributePart;
use Svoboda\PsrRouter\Route\Parts\MainPart;
use Svoboda\PsrRouter\Route\Parts\OptionalPart;
use Svoboda\PsrRouter\Route\Parts\StaticPart;

/**
 * Two-pass visitor of all parts of the route.
 */
abstract class PartsVisitor
{
    /**
     * Enters the attribute part.
     *
     * @param AttributePart $part
     */
    public function enterAttribute(AttributePart $part): void
    {
        //
    }

    /**
     * Leaves the attribute part.
     *
     * @param AttributePart $part
     */
    public function leaveAttribute(AttributePart $part): void
    {
        //
    }

    /**
     * Enters the main part.
     *
     * @param MainPart $part
     */
    public function enterMain(MainPart $part): void
    {
        //
    }

    /**
     * Leaves the main part.
     *
     * @param MainPart $part
     */
    public function leaveMain(MainPart $part): void
    {
        //
    }

    /**
     * Enters the optional part.
     *
     * @param OptionalPart $part
     */
    public function enterOptional(OptionalPart $part): void
    {
        //
    }

    /**
     * Leaves the optional part.
     *
     * @param OptionalPart $part
     */
    public function leaveOptional(OptionalPart $part): void
    {
        //
    }

    /**
     * Enters the static part.
     *
     * @param StaticPart $part
     */
    public function enterStatic(StaticPart $part): void
    {
        //
    }

    /**
     * Leaves the static part.
     *
     * @param StaticPart $part
     */
    public function leaveStatic(StaticPart $part): void
    {
        //
    }
}
