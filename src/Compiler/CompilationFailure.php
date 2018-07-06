<?php

namespace Svoboda\Router\Compiler;

use Svoboda\Router\Exception;

/**
 * The route could not be compiled.
 */
class CompilationFailure extends Exception
{
    /**
     * The attribute has unknown type.
     *
     * @param string $name
     * @param string $type
     * @return CompilationFailure
     */
    public static function unknownType(string $name, string $type): self
    {
        return new self("The attribute '$name' has unknown type '$type'");
    }
}
