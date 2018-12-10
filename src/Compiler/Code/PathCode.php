<?php

declare(strict_types=1);

namespace Svoboda\Router\Compiler\Code;

use Svoboda\Router\Route\Path\AttributePath;
use Svoboda\Router\Route\Path\EmptyPath;
use Svoboda\Router\Route\Path\OptionalPath;
use Svoboda\Router\Route\Path\PathVisitor;
use Svoboda\Router\Route\Path\StaticPath;
use Svoboda\Router\Route\Route;

/**
 * PHP code that performs matching of a single route.
 */
class PathCode extends PathVisitor
{
    /**
     * The route.
     *
     * @var Route
     */
    private $route;

    /**
     * Index of the route in route collection.
     *
     * @var int
     */
    private $index;

    /**
     * The generated matcher code.
     *
     * @var string[]
     */
    private $codes;

    /**
     * Constructor.
     *
     * @param Route $route
     * @param int $index
     */
    public function __construct(Route $route, int $index)
    {
        $this->route = $route;
        $this->index = $index;
        $this->codes = [];

        $route->getPath()->accept($this);

        $pathCode = $this->getNextCode();

        $code = <<<CODE
            
            // route
            
            \$uri = \$path;
            \$matches = [];
            
            $pathCode
            
            // route end

            CODE;

        $this->addCode($code);
    }

    /**
     * @inheritdoc
     */
    public function leaveAttribute(AttributePath $path): void
    {
        $pattern = $path->getTypePattern();

        $nextCode = $this->getNextCode();

        $code = <<<CODE

            // attribute path
            
            if (preg_match("#^($pattern)#", \$uri, \$ms) === 1) {
                \$matches[] = \$ms[1];
                \$uri = substr(\$uri, strlen(\$ms[1]));
            
                $nextCode
            
            }
            
            // attribute path end

            CODE;

        $this->addCode($code);
    }

    /**
     * @inheritdoc
     */
    public function leaveOptional(OptionalPath $path): void
    {
        $methodCode = $this->generateMethodCheck();

        $nextCode = $this->getNextCode();

        $code = <<<CODE

            // optional path
            
            $methodCode
            
            $nextCode
            
            $methodCode
            
            // optional path end

            CODE;

        $this->addCode($code);
    }

    /**
     * @inheritdoc
     */
    public function leaveStatic(StaticPath $path): void
    {
        $static = $path->getStatic();
        $staticLength = strlen($static);

        $nextCode = $this->getNextCode();

        $code = <<<CODE

            // static path
            
            if (strpos(\$uri, "$static") === 0) {
                \$uri = substr(\$uri, $staticLength);
            
                 $nextCode
            
            }
            
            // static path end

            CODE;

        $this->addCode($code);
    }

    /**
     * @inheritdoc
     */
    public function leaveEmpty(EmptyPath $path): void
    {
        $methodCode = $this->generateMethodCheck();

        $this->addCode($methodCode);
    }

    /**
     * Generates a code that performs the method check.
     *
     * @return string
     */
    public function generateMethodCheck(): string
    {
        $method = $this->route->getMethod();
        $index = $this->index;

        return <<<CODE

            // method check
            
            if (\$uri === "") {
                if (\$method === "$method") {
                    return $index;
                } else {
                    \$allowed["$method"] = $index;
                }
            }
            
            // method check end

            CODE;
    }

    /**
     * Converts the object to a string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->codes[0];
    }

    /**
     * Stores the code for later use.
     *
     * @param string $code
     */
    private function addCode(string $code): void
    {
        $this->codes[] = $code;
    }

    /**
     * Returns the code of the next code path.
     *
     * @return string
     */
    private function getNextCode(): string
    {
        return array_pop($this->codes) ?? "";
    }
}
