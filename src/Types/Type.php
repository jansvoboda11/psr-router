<?php

declare(strict_types=1);

namespace Svoboda\Router\Types;

class Type
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $pattern;

    /**
     * Constructor.
     * @param string $name
     * @param string $pattern
     * @throws InvalidTypes
     */
    public function __construct(string $name, string $pattern)
    {
        if (!preg_match("#^\w+$#", $name)) {
            throw InvalidTypes::invalidTypeName($name);
        }

        if (@preg_match("#$pattern#", "") === false) {
            throw InvalidTypes::invalidTypePattern($name, $pattern);
        }
        
        $this->name = $name;
        $this->pattern = $pattern;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }
}
