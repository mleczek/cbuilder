<?php

namespace Mleczek\CBuilder\Repository\Providers;

use Mleczek\CBuilder\Package\Package;
use Mleczek\CBuilder\Repository\Exceptions\PackageNotFoundException;
use Mleczek\CBuilder\Repository\Exceptions\RepositorySourceNotExistsException;
use Mleczek\CBuilder\Repository\Repository;

/**
 * Empty repository used as a parent for the root package.
 */
class EmptyRepository implements Repository
{
    /**
     * @param string $package
     * @return bool
     */
    public function has($package)
    {
        return false;
    }

    /**
     * @param string $package
     * @return Package
     * @throws PackageNotFoundException
     */
    public function get($package)
    {
        throw new PackageNotFoundException("Cannot find '$package' package in the repository which by definition is without packages.'");
    }

    /**
     * @param string $src
     * @throws RepositorySourceNotExistsException
     */
    public function setSource($src)
    {
        throw new RepositorySourceNotExistsException("The empty repository cannot contain any source by definition.");
    }

    /**
     * @return string
     * @throws RepositorySourceNotExistsException
     */
    public function getSource()
    {
        throw new RepositorySourceNotExistsException("The empty repository cannot contain any source by definition.");
    }
}
