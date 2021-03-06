<?php

namespace Mleczek\CBuilder\Dependency;

use Mleczek\CBuilder\Dependency\Exceptions\DependencyNotAvailableException;
use Mleczek\CBuilder\Package\Factory;
use Mleczek\CBuilder\Package\Remote;
use Mleczek\CBuilder\Version\Comparator;

/**
 * Gather information about installed and required packages
 * and give information about missing, outdated and redundant
 * dependencies to achieve consistent state.
 */
class Collector
{
    /**
     * @var Observer
     */
    private $observer;

    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var Comparator
     */
    private $comparator;

    /**
     * @var string[] Packages names.
     */
    private $redundant = [];

    /**
     * @var string[] Package name (key) with new version (value).
     */
    private $outdated = [];

    /**
     * @var string[] Package name (key) with version (value).
     */
    private $missing = [];

    /**
     * Collector constructor.
     *
     * @param Observer $observer
     * @param Resolver $resolver
     * @param Factory $factory
     * @param Comparator $comparator
     */
    public function __construct(Observer $observer, Resolver $resolver, Factory $factory, Comparator $comparator)
    {
        $this->observer = $observer;
        $this->resolver = $resolver;
        $this->factory = $factory;
        $this->comparator = $comparator;
    }

    /**
     * Find and set state in which all of dependencies meet all conditions
     * (use the newest version of the package if there is more than one available).
     *
     * @throws DependencyNotAvailableException
     */
    public function collect()
    {
        // Update current status for installed and required packages.
        $this->observer->observe();
        $this->resolver->resolve($this->factory->makeCurrent());

        // Reset to default state.
        $this->missing = [];
        $this->outdated = [];
        $this->redundant = $this->observer->getAmbiguous();

        // Establish new versions for each package.
        $required = $this->resolver->getList();
        foreach ($required as $item) {
            $constraints = $this->resolveConstraints($item);
            $versions = $item->remote->getVersionFinder()
                ->getSatisfiedBy($constraints);

            // Fail if no available versions found.
            if (empty($versions)) {
                $packageName = $item->remote->getPackage()->getName();
                throw new DependencyNotAvailableException("Cannot find '$packageName' package meeting given constraints: '$constraints'.");
            }

            // Sort versions and bind the newest one.
            $versions = $this->comparator->sort($versions);
            $this->bind($item->remote, array_pop($versions));
        }
    }

    /**
     * Get constraints for the given entry,
     * include constraint assuming that the new version
     * will be greater or equal current version.
     *
     * @see resolver::getList
     * @param object $item
     * @return string
     */
    private function resolveConstraints($item)
    {
        $constraints = $item->constraints;
        $packageName = $item->remote->getPackage()->getName();

        // If package isn't currently installed then
        // we just use resolved constraints from other packages.
        if (!$this->observer->hasInstalled($packageName)) {
            return $constraints;
        }

        // Get currently installed version.
        $currentVersion = $this->observer->getInstalled()[$packageName];

        // Force to use specified version if root package json still
        // requires this version of package (guarantee repeatable installs).
        if ($this->isPackageVersionRequired($packageName, $currentVersion)) {
            return $currentVersion;
        }

        // If it is possible then keep the current version.
        $joinedConstraints = implode(' ', $constraints);
        if ($this->comparator->satisfies($currentVersion, $joinedConstraints)) {
            return $currentVersion;
        }

        // In other way allow only upgrades (downgrades aren't allowed).
        return "$joinedConstraints >=$currentVersion";
    }

    /**
     * Check whether provided package version is required to be used
     * (project package json not changed and requires specified version).
     *
     * @param string $packageName
     * @param string $version
     * @return bool True if provided package version must be used, false otherwise.
     */
    private function isPackageVersionRequired($packageName, $version)
    {
        $dependencies = $this->factory->makeCurrent()->getDependencies();
        foreach ($dependencies as $dependency) {
            if ($dependency->name == $packageName) {
                return $this->comparator->satisfies($version, $dependency->version);
            }
        }

        return false;
    }

    /**
     * Bind remote to the one of the categories.
     *
     * @param Remote $remote
     * @param string $version
     */
    private function bind($remote, $version)
    {
        $packageName = $remote->getPackage()->getName();

        // Update package from unknown to specified version.
        if (($key = array_search($packageName, $this->redundant)) !== false) {
            unset($this->redundant[$key]);
            $this->outdated[$packageName] = $version;
            return;
        }

        // Update package from older to newest version.
        if ($this->observer->hasInstalled($packageName)) {
            // Skip if this is the same version.
            if ($this->observer->getInstalled()[$packageName] === $version) {
                return;
            }

            $this->outdated[$packageName] = $version;
            return;
        }

        // Install new package.
        $this->missing[$packageName] = $version;
    }

    /**
     * @return string[] Packages names.
     */
    public function getRedundant()
    {
        return $this->redundant;
    }

    /**
     * @return string[] Package name (key) with new version (value).
     */
    public function getOutdated()
    {
        return $this->outdated;
    }

    /**
     * @return string[] Package name (key) with version (value).
     */
    public function getMissing()
    {
        return $this->missing;
    }
}
