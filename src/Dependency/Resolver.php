<?php

namespace Mleczek\CBuilder\Dependency;

use Mleczek\CBuilder\Dependency\Entities\Factory;
use Mleczek\CBuilder\Package\Package;
use Mleczek\CBuilder\Package\Remote;
use Mleczek\CBuilder\Repository\Collection;
use Mleczek\CBuilder\Repository\Exceptions\PackageNotFoundException;
use Mleczek\CBuilder\Repository\Factory as RepositoriesFactory;
use Mleczek\CBuilder\Dependency\Entities\Factory as TreeNodeFactory;

/**
 * Resolve dependencies tree from cbuilder.json file
 * (include nested dependencies with loops detection).
 *
 * Repositories defined in the cbuilder.json file are skipped for the dependencies.
 * This prevents packages names conflicts, realize the situation when A has 2 dependencies:
 * B and C, the B also has the C dependency (but due to the specific repositories definition
 * the C package is resolved from the other repository).
 *
 * There are 2 major use cases of the repositories:
 * - to register private repository shared between all company packages,
 * - and to improve development process experience.
 */
class Resolver
{
    /**
     * Dependencies tree.
     *
     * @var object[] Each object contains remote, constraints and dependencies key.
     */
    private $tree = [];

    /**
     * Dependencies list.
     *
     * @var object[string] Package name (key) with object (value) containing remote and constraints key.
     */
    private $list = [];

    /**
     * @var RepositoriesFactory
     */
    private $repositoriesFactory;

    /**
     * @var TreeNodeFactory
     */
    private $treeNodeFactory;

    /**
     * Repositories for the latest resolved package.
     *
     * @var Collection
     */
    private $repositories;

    /**
     * Resolver constructor.
     *
     * @param RepositoriesFactory $repositoriesFactory
     * @param TreeNodeFactory $treeNodeFactory
     */
    public function __construct(RepositoriesFactory $repositoriesFactory, TreeNodeFactory $treeNodeFactory)
    {
        $this->repositoriesFactory = $repositoriesFactory;
        $this->treeNodeFactory = $treeNodeFactory;
    }

    /**
     * Get dependencies tree.
     *
     * @return object[] Each object contains remote, constraints and dependencies key.
     */
    public function getTree()
    {
        return $this->tree;
    }

    /**
     * Get dependencies list.
     *
     * @return object[string] Each object contains remote and constraints key.
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * @param Package $package
     * @return $this
     * @throws PackageNotFoundException
     */
    public function resolve(Package $package)
    {
        // Get repositories only for this package
        // in which dependencies will be searched.
        $plainRepositories = $package->getRepositories();
        $this->repositories = $this->repositoriesFactory->hydrate($plainRepositories);

        // For root package always get all
        // dependencies including dev ones.
        $dependencies = array_merge(
            $package->getDependencies(),
            $package->getDevDependencies()
        );

        // Register each dependency in tree and list.
        foreach ($dependencies as $dependency) {
            $remote = $this->repositories->find($dependency->name);

            $this->registerTree($remote, $dependency->version);
            $this->registerList($remote, $dependency->version);
        }

        return $this;
    }

    /**
     * @param Remote $dependency
     * @param string $constraint
     * @see $list
     */
    private function registerList(Remote $dependency, $constraint)
    {
        $packageName = $dependency->getPackage()->getName();

        // Initialize new dependency in the list.
        if (!isset($this->list[$packageName])) {
            $this->list[$packageName] = (object)[
                'remote' => $dependency,
                'constraints' => [],
            ];
        }

        // Add new constraints.
        $entry = $this->list[$packageName];
        $entry->constraints[] = $constraint;

        // Register nested dependencies.
        $dependencies = $dependency->getPackage()->getDependencies();
        foreach ($dependencies as $dependency) {
            $remote = $this->repositories->find($dependency->name);

            $this->registerList($remote, $dependency->version);
        }
    }

    /**
     * @param Remote $dependency
     * @param string $constraint
     * @see $tree
     */
    private function registerTree(Remote $dependency, $constraint)
    {
        $this->tree[] = $this->treeNodeFactory->makeTreeNode($this->repositories, null, $dependency, $constraint);
    }
}
