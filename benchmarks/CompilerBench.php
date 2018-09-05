<?php

declare(strict_types=1);

namespace SvobodaBench\Router;

use Svoboda\Router\Compiler\Compiler;
use Svoboda\Router\RouteCollection;
use Svoboda\Router\Router;

/**
 * @Iterations(10)
 * @Revs({50})
 */
class CompilerBench
{
    use BenchmarkHelper;

    /** @var RouteCollection */
    private $routes;

    /** @var Compiler[] */
    private $compilers;

    const COMPILER_MULTI_PATTERN = "multi pattern";
    const COMPILER_SINGLE_PATTERN = "single pattern";
    const COMPILER_TREE_PATTERN = "tree pattern";
    const COMPILER_LINEAR_CODE = "linear code";
    const COMPILER_TREE_CODE = "tree code";

    const REQUEST_FIRST_ROUTE = "first route";
    const REQUEST_LAST_ROUTE = "last route";
    const REQUEST_NO_ROUTE = "no route";

    public function __construct()
    {
        $this->routes = $this->createRoutes(500);

        $this->compilers = [
            self::COMPILER_MULTI_PATTERN => self::createMultiPatternCompiler(),
            self::COMPILER_SINGLE_PATTERN => self::createSinglePatternCompiler(),
            self::COMPILER_TREE_PATTERN => self::createTreePatternCompiler(),
            self::COMPILER_LINEAR_CODE => self::createLinearCodeCompiler(),
            self::COMPILER_TREE_CODE => self::createTreeCodeCompiler(),
        ];

//        self::showRoutes($this->routes);
    }

    /**
     * @Groups({"compilers"})
     * @ParamProviders({"provideCompilers"})
     *
     * @param array $params
     */
    public function bench_compilers(array $params): void
    {
        /** @var Compiler $compiler */
        $compiler = $this->compilers[$params["compiler"]];

        new Router($this->routes, $compiler);
    }

    public function provideCompilers(): array
    {
        return [
            ["compiler" => self::COMPILER_MULTI_PATTERN],
            ["compiler" => self::COMPILER_SINGLE_PATTERN],
            ["compiler" => self::COMPILER_TREE_PATTERN],
            ["compiler" => self::COMPILER_LINEAR_CODE],
            ["compiler" => self::COMPILER_TREE_CODE],
        ];
    }
}
