<?php

namespace Mleczek\CBuilder\Tests\Dependency\Entities;

use Mleczek\CBuilder\Dependency\Entities\Factory;
use Mleczek\CBuilder\Dependency\Entities\TreeNode;
use Mleczek\CBuilder\Dependency\Exceptions\DependenciesLoopException;
use Mleczek\CBuilder\Package\Package;
use Mleczek\CBuilder\Package\Remote;
use Mleczek\CBuilder\Repository\Collection;
use Mleczek\CBuilder\Repository\Factory as RepositoriesFactory;
use Mleczek\CBuilder\Repository\Repository;
use Mleczek\CBuilder\Tests\TestCase;

class TreeNodeTest extends TestCase
{
    /**
     * @var Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $repositories;

    /**
     * @var Factory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $factory;

    /**
     * @var TreeNode|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $parent;

    /**
     * @var Remote|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $remote;

    /**
     * @var string
     */
    protected $constraint;

    public function setUp()
    {
        $this->repositories = $this->createMock(Collection::class);
        $this->factory = $this->createMock(Factory::class);
        $this->parent = $this->createMock(TreeNode::class);
        $this->remote = $this->createMock(Remote::class);
        $this->constraint = '1.0';
    }

    public function testLoopsDetection()
    {
        // Node has only one parent.
        $this->parent->expects($this->any())
            ->method('getParent')
            ->willReturn(null);

        // Which is the same package.
        $this->parent->method('getRemote')
            ->willReturn($this->remote);

        $package = $this->createMock(Package::class);
        $package->method('getName')
            ->willReturn('org/package');

        // Return same package and repository for node and parent node.
        $this->remote->method('getPackage')
            ->willReturn($package);

        // Test if "loop" detection (self-reference).
        $this->expectException(DependenciesLoopException::class);
        $treeNode = new TreeNode(
            $this->factory,
            $this->repositories,
            $this->parent,
            $this->remote,
            $this->constraint
        );
    }

    public function testResolvingDependencies()
    {
        $dependencies = ['a' => '*', 'b' => '1.0', 'c' => '^3.5.2'];

        // Define package dependencies.
        $package = $this->createMock(Package::class);
        $package->expects($this->once())
            ->method('getDependencies')
            ->willReturn($dependencies);

        // For each dependency return any remote class.
        $this->repositories->expects($this->exactly(3))
            ->method('find')
            ->withConsecutive(['a'], ['b'], ['c'])
            ->willReturn($this->createMock(Remote::class));

        $this->remote->method('getPackage')
            ->willReturn($package);

        // Should be created 3 nodes.
        $this->factory->expects($this->exactly(3))
            ->method('makeTreeNode')
            ->willReturnOnConsecutiveCalls('x', 'y', 'z');

        $treeNode = new TreeNode(
            $this->factory,
            $this->repositories,
            null,
            $this->remote,
            $this->constraint
        );

        $this->assertEquals(['x', 'y', 'z'], $treeNode->getDependencies());
    }
}
