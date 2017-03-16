<?php

namespace Mleczek\CBuilder\Repository;

use Mleczek\CBuilder\Package\Package;
use Mleczek\CBuilder\Package\Remote;
use Mleczek\CBuilder\Repository\Exceptions\PackageNotFoundException;

class Collection
{
    /**
     * @var Repository[]
     */
    private $repositories = [];

    /**
     * Add new repository to the collection.
     * Repositories are searched in order in which they have been added.
     *
     * @param Repository $repo
     */
    public function add(Repository $repo)
    {
        $this->repositories[] = $repo;
    }

    /**
     * @param string $package
     * @return bool
     */
    public function has($package)
    {
        foreach ($this->repositories as $repository) {
            if ($repository->has($package)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get package from the first repository containing it.
     *
     * @param string $package
     * @return Remote
     * @throws PackageNotFoundException
     */
    public function find($package)
    {
        foreach ($this->repositories as $repository) {
            if ($repository->has($package)) {
                return $repository->get($package);
            }
        }

        $repositoriesCount = count($this->repositories);
        throw new PackageNotFoundException("The '$package' package was not found in the repositories collection (containing $repositoriesCount repositories).'");
    }
}
