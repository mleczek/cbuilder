<?php


namespace Mleczek\CBuilder\Versions;

interface VersionsProvider
{
    /**
     * @param string $source Local dir, git url - depends on the repository provider.
     * @return $this
     */
    public function from($source);

    /**
     * @return string[]
     */
    public function all();

    /**
     * @param string $constraint
     * @return bool
     */
    public function has($constraint);

    /**
     * @param string $version
     * @return string[]
     */
    public function greaterThan($version);
}
