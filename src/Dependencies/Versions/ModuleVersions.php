<?php


namespace Mleczek\CBuilder\Dependencies\Versions;


/**
 * Get information about available module versions.
 */
interface ModuleVersions
{
    /**
     * Get all available package versions.
     *
     * @return string[]
     */
    public function all();

    /**
     * Get newer versions than the specified.
     *
     * @param string $version
     * @return string[]
     */
    public function greaterThan($version);
}