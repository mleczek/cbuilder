<?php

namespace Mleczek\CBuilder\Repository;

use Mleczek\CBuilder\Package\Package;
use Mleczek\CBuilder\Package\Remote;
use Mleczek\CBuilder\Repository\Exceptions\PackageNotFoundException;

/**
 * Repository constructor can accept argument with name $src
 * (the value of "type" field in section "repositories" in package json).
 */
interface Repository
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
    public function getId();

    /**
     * @param string $package
     * @return bool
     */
    public function has($package);

    /**
     * @param string $package
     * @return Remote
     * @throws PackageNotFoundException
     */
    public function get($package);
}
