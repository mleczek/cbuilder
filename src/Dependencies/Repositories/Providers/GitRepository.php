<?php


namespace Mleczek\CBuilder\Dependencies\Repositories\Providers;


use Mleczek\CBuilder\Dependencies\Module;
use Mleczek\CBuilder\Dependencies\Repositories\Repository;

/**
 * Git repository storage provider.
 *
 * If repository path is set to "https://git.example.com" then package "org/package"
 * must be accessible at the "https://git.example.com/org/package" address.
 */
class GitRepository implements Repository
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