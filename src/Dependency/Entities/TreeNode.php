<?php

namespace Mleczek\CBuilder\Dependency\Entities;

use Mleczek\CBuilder\Dependency\Entities\Factory;
use Mleczek\CBuilder\Dependency\Exceptions\DependenciesLoopException;
use Mleczek\CBuilder\Package\Remote;
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
    protected $dependecies = [];

    /**
     * @var RepositoriesFactory
     */
    private $repositoriesFactory;

    /**
     * @var Factory
     */
    private $factory;

    /**
     * TreeNode constructor.
     *
     * @param RepositoriesFactory $repositoriesFactory
     * @param Factory $factory
     * @param TreeNode|null $parent
     * @param Remote $remote
     * @param string $constraint
     * @throws DependenciesLoopException
     */
    public function __construct(RepositoriesFactory $repositoriesFactory, Factory $factory, TreeNode $parent = null, Remote $remote, $constraint)
    {
        $this->repositoriesFactory = $repositoriesFactory;
        $this->factory = $factory;

        $this->parent = $parent;
        $this->remote = $remote;
        $this->constraint = $constraint;
        $this->resolveDependencies();
    }

    /**
     * Fill in dependencies property.
     *
     * @see $dependecies
     * @throws DependenciesLoopException
     */
    public function resolveDependencies()
    {
        $this->throwIfDependencyLoopExists();

        // Get repositories only for this package
        // in which dependencies will be searched.
        $plainRepositories = $this->remote->getPackage()->getRepositories();
        $repositories = $this->repositoriesFactory->hydrate($plainRepositories);

        // Register each dependency in result object.
        $dependencies = $this->remote->getPackage()->getDependencies();
        foreach ($dependencies as $packageName => $constraint) {
            $remote = $repositories->find($packageName);
            $this->dependecies[] = $this->factory->makeTreeNode($this, $remote, $constraint);
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
            $repositoryId = $this->getRemote()->getRepository()->getId();

            $parentPackageName = $parent->getRemote()->getPackage()->getName();
            $parentRepositoryId = $parent->getRemote()->getRepository()->getId();

            if ($packageName === $parentPackageName && $repositoryId === $parentRepositoryId) {
                $parentPackageName = $this->getParent()->getRemote()->getPackage()->getName();
                throw new DependenciesLoopException("Detected dependencies loop: $packageName [-> ...] -> $parentPackageName -> $packageName (in '$repositoryId' repository)");
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
    public function getDependecies()
    {
        return $this->dependecies;
    }
}
