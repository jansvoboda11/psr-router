<?php

declare(strict_types=1);

namespace SvobodaTest\Router\Unit\Compiler\Tree;

use Prophecy\Prophecy\ObjectProphecy;
use Svoboda\Router\Compiler\Tree\AttributeNode;
use Svoboda\Router\Compiler\Tree\LeafNode;
use Svoboda\Router\Compiler\Tree\OptionalNode;
use Svoboda\Router\Compiler\Tree\StaticNode;
use Svoboda\Router\Compiler\Tree\Tree;
use Svoboda\Router\Compiler\Tree\TreeFactory;
use Svoboda\Router\Route\Path\AttributePath;
use Svoboda\Router\Route\Path\OptionalPath;
use Svoboda\Router\Route\Path\PathSerializer;
use Svoboda\Router\Route\Path\StaticPath;
use Svoboda\Router\Route\Route;
use Svoboda\Router\RouteCollection;
use Svoboda\Router\Types\Type;
use SvobodaTest\Router\FakeHandler;
use SvobodaTest\Router\TestCase;

class TreeFactoryTest extends TestCase
{
    /** @var Type */
    private $number;

    /** @var ObjectProphecy|RouteCollection */
    private $routes;

    /** @var TreeFactory */
    private $factory;

    protected function setUp()
    {
        $this->number = new Type("number", "\d+");
        $this->routes = $this->prophesize(RouteCollection::class);
        $this->factory = new TreeFactory(new PathSerializer());
    }

    public function test_linear_tree_is_creates()
    {
        $path = new StaticPath(
            "/api/users/",
            new AttributePath(
                "id",
                $this->number,
                new OptionalPath(
                    new StaticPath(
                        "/show"
                    )
                )
            )
        );

        $route = new Route("GET", $path, new FakeHandler());

        $this->routes->all()->willReturn([$route]);

        $tree = $this->factory->create($this->routes->reveal());

        $expectedTree = new Tree([
            new StaticNode(
                "/api/users/",
                [
                    new AttributeNode(
                        "id",
                        $this->number,
                        [
                            new OptionalNode(
                                [
                                    new StaticNode(
                                        "/show",
                                        [
                                            new LeafNode(
                                                $route
                                            )
                                        ]
                                    )
                                ]
                            )
                        ]
                    )
                ]
            )
        ]);

        self::assertEquals($expectedTree, $tree);
    }
}
