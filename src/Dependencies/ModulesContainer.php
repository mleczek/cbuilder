<?php


namespace Mleczek\CBuilder\Dependencies;


/**
 * Store information about package dependencies
 * and all module dependencies (all nesting levels).
 */
class ModulesContainer
{
    /**
     * @param string $package
     * @param string $version
     */
    public function add($package, $version)
    {
        if($this->conflicts($package, $version)) {
            // TODO: Throw exception...
        }

        // TODO: ...
    }

    /**
     * Check whether given package in specific version
     * conflicts with already added packages.
     *
     * @param string $package
     * @param string $version
     * @return bool True if in conflict, false otherwise.
     */
    public function conflicts($package, $version)
    {
        // TODO: ...
    }
}