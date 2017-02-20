<?php

namespace Mleczek\CBuilder\Repository;

use Mleczek\CBuilder\Package\Package;
use Mleczek\CBuilder\Repository\Exceptions\PackageNotFoundException;

interface Repository
{
    /**
     * @param string $src
     */
    public function setSource($src);

    /**
     * @return string
     */
    public function getSource();

    /**
     * @param string $package
     * @return bool
     */
    public function has($package);

    /**
     * @param string $package
     * @return Package
     * @throws PackageNotFoundException
     */
    public function get($package);
}
