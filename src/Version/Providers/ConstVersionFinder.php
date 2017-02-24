<?php

namespace Mleczek\CBuilder\Version\Providers;

use Mleczek\CBuilder\Version\Comparator;
use Mleczek\CBuilder\Version\Finder;

class ConstVersionFinder implements Finder
{
    const VERSION = '1.0.0';

    /**
     * @var Comparator
     */
    private $comparator;

    /**
     * ConstVersionFinder constructor.
     *
     * @param Comparator $comparator
     * @param string $package
     */
    public function __construct(Comparator $comparator, $package)
    {
        $this->comparator = $comparator;
    }

    /**
     * @param string $version
     * @return bool
     */
    public function has($version)
    {
        return $this->comparator->equalTo($version, self::VERSION);
    }

    /**
     * Get all available package versions.
     *
     * @return string[]
     */
    public function get()
    {
        return [self::VERSION];
    }

    /**
     * Get versions which satisfy constraint.
     *
     * @param string $constraint Version constraint (eq. ">= 5.3").
     * @return string[]
     */
    public function getSatisfiedBy($constraint)
    {
        $versions = $this->get();

        return $this->comparator->satisfiedBy($versions, $constraint);
    }

    /**
     * @param string $version
     * @return string[]
     */
    public function getGreaterThan($version)
    {
        $versions = $this->get();

        return $this->comparator->satisfiedBy($versions, "> $version");
    }
}
