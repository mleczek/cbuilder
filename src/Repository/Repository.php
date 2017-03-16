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
