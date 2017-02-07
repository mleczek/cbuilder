<?php


namespace Mleczek\CBuilder\Dependencies\Versions\Providers;


use Mleczek\CBuilder\Dependencies\Versions\ModuleVersions;

/**
 * Read module versions from git repository tags.
 *
 * Tags must be in format "[v]<major>[.<minor>[.<patch>]]":
 * - v0.1.0, v1.5, 1.2.29, v3, 3.11
 */
class GitTagsVersions implements ModuleVersions
{
    /**
     * Get all available package versions.
     *
     * @return string[]
     */
    public function all()
    {
        // TODO: Implement all() method.
    }

    /**
     * Get newer versions than the specified.
     *
     * @param string $version
     * @return string[]
     */
    public function greaterThan($version)
    {
        // TODO: Implement greaterThan() method.
    }
}