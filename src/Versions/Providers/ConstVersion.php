<?php


namespace Mleczek\CBuilder\Versions\Providers;

use Mleczek\CBuilder\Versions\Comparator;
use Mleczek\CBuilder\Versions\VersionsProvider;

class ConstVersion implements VersionsProvider
{
    /**
     * @var Comparator
     */
    private $comparator;

    /**
     * @param Comparator $versions
     */
    public function __construct(Comparator $versions)
    {
        $this->comparator = $versions;
    }

    /**
     * @param string $source Local dir, git url - depends on the repository provider.
     * @return $this
     */
    public function from($source)
    {
        // Works with any source

        return $this;
    }

    /**
     * @return string[]
     */
    public function all()
    {
        return ['1.0.0'];
    }

    /**
     * @param string $constraint
     * @return bool
     */
    public function has($constraint)
    {
        $versions = $this->comparator->satisfiedBy($this->all(), $constraint);

        return count($versions) > 0;
    }

    /**
     * @param string $version
     * @return string[]
     */
    public function greaterThan($version)
    {
        return $this->comparator->satisfiedBy([$version], '> 1.0.0');
    }
}
