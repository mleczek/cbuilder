<?php

namespace Mleczek\CBuilder\Dependency\Entities;

use Mleczek\CBuilder\Dependency\Entities\Factory;
use Mleczek\CBuilder\Dependency\Exceptions\DependenciesLoopException;
use Mleczek\CBuilder\Package\Remote;
use Mleczek\CBuilder\Repository\Collection;
use Mleczek\CBuilder\Repository\Factory as RepositoriesFactory;

class TreeNode
{
    /**
     * @var TreeNode
     */
    protected $parent = null;

    /**
     * @var Remote
     */
    protected $remote;

    /**
     * @var string
     */
    protected $constraint = [];

    /**
     * @var TreeNode[]
     */
    protected $dependencies = [];

    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var Collection
     */
    private $repositories;

    /**
     * TreeNode constructor.
     *
     * @param Factory $factory
     * @param Collection $repositories Repositories in which dependencies will be searched.
     * @param TreeNode|null $parent
     * @param Remote $remote
     * @param string $constraint
     */
    public function __construct(Factory $factory, Collection $repositories, TreeNode $parent = null, Remote $remote, $constraint)
    {
        $this->factory = $factory;

        $this->repositories = $repositories;
        $this->parent = $parent;
        $this->remote = $remote;
        $this->constraint = $constraint;
        $this->resolveDependencies();
    }

    /**
     * Fill in dependencies property.
     *
     * @see $dependencies
     * @throws DependenciesLoopException
     */
    public function resolveDependencies()
    {
        $this->throwIfDependencyLoopExists();
        // Register each dependency in result object.
        $dependencies = $this->remote->getPackage()->getDependencies();
        foreach ($dependencies as $packageName => $constraint) {
            $remote = $this->repositories->find($packageName);
            $this->dependencies[] = $this->factory->makeTreeNode($this->repositories, $this, $remote, $constraint);
        }
    }

    /**
     * @throws DependenciesLoopException
     */
    protected function throwIfDependencyLoopExists()
    {
        $parent = $this->getParent();
        while (!is_null($parent)) {
            $packageName = $this->getRemote()->getPackage()->getName();
            $parentPackageName = $parent->getRemote()->getPackage()->getName();

            if ($packageName === $parentPackageName) {
                $parentPackageName = $this->getParent()->getRemote()->getPackage()->getName();
                throw new DependenciesLoopException("Detected dependencies loop: $packageName [-> ...] -> $parentPackageName -> $packageName");
            }

            $parent = $parent->getParent();
        }
    }

    /**
     * @return TreeNode|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return Remote
     */
    public function getRemote()
    {
        return $this->remote;
    }

    /**
     * @return string
     */
    public function getConstraint()
    {
        return $this->constraint;
    }

    /**
     * @return TreeNode[]
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }
}
