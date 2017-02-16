<?php

namespace Mleczek\CBuilder\Console\Tools;

use Mleczek\CBuilder\Repositories\Container;
use Mleczek\CBuilder\Repositories\Factory as RepoFactory;

class RepositoriesManager
{
    /**
     * @var RepoFactory
     */
    private $factory;

    /**
     * Package name (key) with assigned container (value).
     *
     * @var Container[]
     */
    private $containers = [];

    /**
     * @param RepoFactory $factory
     */
    public function __construct(RepoFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Get container for working directory package.
     *
     * @return Container
     */
    public function getDefault()
    {
        static $container = null;
        if (is_null($container)) {
            $container = $this->factory->makeContainer();
        }

        return $container;
    }

    /**
     * Get container for the given package name.
     *
     * @param string $package
     * @return Container
     */
    public function get($package)
    {
        if (!isset($this->containers[$package])) {
            $this->containers[$package] = $this->factory->makeContainer();
        }

        return $this->containers[$package];
    }
}
