<?php

namespace Mleczek\CBuilder\Version\Providers;

use Mleczek\CBuilder\Version\Comparator;
use Mleczek\CBuilder\Version\Resolver;

class ConstVersionResolver implements Resolver
{
    const VERSION = '1.0.0';

    /**
     * @var Comparator
     */
    private $comparator;

    /**
     * ConstVersionResolver constructor.
     *
     * @param Comparator $comparator
     */
    public function __construct(Comparator $comparator)
    {
        $this->comparator = $comparator;
    }

    /**
     * @param string $package
     * @param string $version
     * @return bool
     */
    public function has($package, $version)
    {
        return $this->comparator->equalTo($version, self::VERSION);
    }

    /**
     * Get all available package versions.
     *
     * @param string $package
     * @return string[]
     */
    public function get($package)
    {
        return [self::VERSION];
    }

    /**
     * Get versions which satisfy constraint.
     *
     * @param string $package
     * @param string $constraint Version constraint (eq. ">= 5.3").
     * @return string[]
     */
    public function getSatisfiedBy($package, $constraint)
    {
        $versions = $this->get($package);

        return $this->comparator->satisfiedBy($versions, $constraint);
    }

    /**
     * @param string $package
     * @param string $version
     * @return string[]
     */
    public function getGreaterThan($package, $version)
    {
        $versions = $this->get($package);

        return $this->comparator->satisfiedBy($versions, "> $version");
    }
}
