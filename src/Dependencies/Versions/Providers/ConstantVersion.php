<?php


namespace Mleczek\CBuilder\Dependencies\Versions\Providers;


use Mleczek\CBuilder\Dependencies\Versions\ModuleVersions;
use Mleczek\CBuilder\Dependencies\Versions\VersionsHelper;

/**
 * Serve module always as the same version.
 */
class ConstantVersion implements ModuleVersions
{
    /**
     * @var string
     */
    const VERSION = '1.0.0';

    /**
     * @Inject
     * @var VersionsHelper
     */
    private $semver;

    /**
     * Get all available package versions.
     *
     * @return string[]
     */
    public function all()
    {
        return [self::VERSION];
    }

    /**
     * Get newer versions than the specified.
     *
     * @param string $version
     * @return string[]
     */
    public function greaterThan($version)
    {
        if($this->semver->greaterThan(self::VERSION, $version)) {
            return [self::VERSION];
        }

        return [];
    }
}