<?php


namespace Mleczek\CBuilder\Dependencies\Repositories;


use Mleczek\CBuilder\Dependencies\Module;

/**
 * Store information about collections of packages.
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
     * @return Module
     */
    public function get($package);
}