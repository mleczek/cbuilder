<?php

namespace Mleczek\CBuilder\Version;

/**
 * Resolve available package versions for given package.
 */
interface Resolver
{
    /**
     * @param string $package
     * @param string $version
     * @return bool
     */
    public function has($package, $version);

    /**
     * Get all available package versions.
     *
     * @param string $package
     * @return string[]
     */
    public function get($package);

    /**
     * Get versions which satisfy constraint.
     *
     * @param string $package
     * @param string $constraint Version constraint (eq. ">= 5.3").
     * @return string[]
     */
    public function getSatisfiedBy($package, $constraint);

    /**
     * @param string $package
     * @param string $version
     * @return string[]
     */
    public function getGreaterThan($package, $version);
}
