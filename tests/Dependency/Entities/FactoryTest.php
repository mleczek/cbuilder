<?php

namespace Mleczek\CBuilder\Tests\Dependency\Entities;

use DI\Container;
use Mleczek\CBuilder\Dependency\Entities\Factory;
use Mleczek\CBuilder\Dependency\Entities\TreeNode;
use Mleczek\CBuilder\Package\Remote;
use Mleczek\CBuilder\Repository\Collection;
use Mleczek\CBuilder\Tests\TestCase;

class FactoryTest extends TestCase
{
    public function testMakeTreeNode()
    {
        $repositories = $this->createMock(Collection::class);
        $parent = $this->createMock(TreeNode::class);
        $remote = $this->createMock(Remote::class);
        $treeNode = $this->createMock(TreeNode::class);

        $di = $this->createMock(Container::class);
        $di->expects($this->once())
            ->method('make')
            ->with(TreeNode::class, [
                'repositories' => $repositories,
                'parent' => $parent,
                'remote' => $remote,
                'constraint' => '^1.2',
            ])->willReturn($treeNode);

        $factory = new Factory($di);
        $this->assertEquals($treeNode, $factory->makeTreeNode($repositories, $parent, $remote, '^1.2'));
    }
}
