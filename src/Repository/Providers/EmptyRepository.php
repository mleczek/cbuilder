<?php

namespace Mleczek\CBuilder\Repository\Providers;

use Mleczek\CBuilder\Package\Package;
use Mleczek\CBuilder\Repository\Exceptions\PackageNotFoundException;
use Mleczek\CBuilder\Repository\Exceptions\RepositorySourceNotExistsException;
use Mleczek\CBuilder\Repository\Repository;
use Mleczek\CBuilder\Package\Remote;

/**
 * Empty repository used as a parent for the root package.
 */
class EmptyRepository implements Repository
{
    /**
     * Get unique identifier. Instances of the repositories
     * with the same type and source should return same identifier.
     *
     * Common pattern is to return identifier in format:
     * <classNamespace>|<sourcePath>
     *
     * @return string
     */
    public function getId()
    {
        return self::class;
    }

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
     * @return Remote
     * @throws PackageNotFoundException
     */
    public function get($package)
    {
        throw new PackageNotFoundException("Cannot find '$package' package in the repository which by definition is without packages.'");
    }
}
