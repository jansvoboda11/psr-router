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

    public function test_one_route_is_transformed()
    {
        $path = new StaticPath("/api/users/",
            new AttributePath("id", $this->number,
                new OptionalPath(
                    new StaticPath("/show")
                )
            )
        );

        $route = new Route("GET", $path, new FakeHandler());

        $this->routes->all()->willReturn([$route]);

        $tree = $this->factory->create($this->routes->reveal());

        $expectedTree = new Tree([
            new StaticNode("/api/users/", [
                new AttributeNode("id", $this->number, [
                    new OptionalNode([
                        new StaticNode("/show", [
                            new LeafNode($route, 0)
                        ])
                    ])
                ])
            ])
        ]);

        self::assertEquals($expectedTree, $tree);
    }

    public function test_two_routes_with_same_path_are_transformed()
    {
        $path = new StaticPath("/users/",
            new AttributePath("id", $this->number)
        );

        $getRoute = new Route("GET", $path, new FakeHandler());
        $deleteRoute = new Route("DELETE", $path, new FakeHandler());

        $this->routes->all()->willReturn([$getRoute, $deleteRoute]);

        $tree = $this->factory->create($this->routes->reveal());

        $expectedTree = new Tree([
            new StaticNode("/users/", [
                new AttributeNode("id", $this->number, [
                    new LeafNode($getRoute, 0),
                    new LeafNode($deleteRoute, 1),
                ])
            ])
        ]);

        self::assertEquals($expectedTree, $tree);
    }

    public function test_two_routes_with_different_paths_are_transformed()
    {
        $path1 = new StaticPath("/users");
        $path2 = new StaticPath("/orders");

        $route1 = new Route("GET", $path1, new FakeHandler());
        $route2 = new Route("GET", $path2, new FakeHandler());

        $this->routes->all()->willReturn([$route1, $route2]);

        $tree = $this->factory->create($this->routes->reveal());

        $expectedTree = new Tree([
            new StaticNode("/users", [
                new LeafNode($route1, 0)
            ]),
            new StaticNode("/orders", [
                new LeafNode($route2, 1)
            ])
        ]);

        self::assertEquals($expectedTree, $tree);
    }
}
