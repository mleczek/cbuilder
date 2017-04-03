<?php

namespace Mleczek\CBuilder\Dependency\Entities;

use DI\Container;
use Mleczek\CBuilder\Dependency\Exceptions\DependenciesLoopException;
use Mleczek\CBuilder\Dependency\Entities\TreeNode;
use Mleczek\CBuilder\Package\Remote;
use Mleczek\CBuilder\Repository\Collection;

class Factory
{
    /**
     * @var Container
     */
    private $di;

    /**
     * Factory constructor.
     *
     * @param Container $di
     */
    public function __construct(Container $di)
    {
        $this->di = $di;
    }

    /**
     * @param Collection $repositories
     * @param TreeNode|null $parent
     * @param Remote $remote
     * @param string $constraint
     * @return \Mleczek\CBuilder\Dependency\Entities\TreeNode
     */
    public function makeTreeNode(Collection $repositories, TreeNode $parent = null, Remote $remote, $constraint)
    {
        return $this->di->make(TreeNode::class, [
            'repositories' => $repositories,
            'parent' => $parent,
            'remote' => $remote,
            'constraint' => $constraint,
        ]);
    }
}
