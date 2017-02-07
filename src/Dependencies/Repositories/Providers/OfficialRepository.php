<?php


namespace Mleczek\CBuilder\Dependencies\Repositories\Providers;


use Mleczek\CBuilder\Dependencies\Module;
use Mleczek\CBuilder\Dependencies\Repositories\Repository;

/**
 * Store meta information about packages.
 *
 * @link https://cbuilder.pl/
 */
class OfficialRepository implements Repository
{
    /**
     * @param string $package
     * @return bool
     */
    public function has($package)
    {
        // TODO: Implement has() method.
    }

    /**
     * @param string $package
     * @return Module
     */
    public function get($package)
    {
        // TODO: Implement download() method.
    }
}