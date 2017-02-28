<?php

namespace Mleczek\CBuilder\Lock;

use Mleczek\CBuilder\Environment\Filesystem;
use Mleczek\CBuilder\Lock\Exceptions\DuplicateLockEntryException;
use Mleczek\CBuilder\Lock\Exceptions\NotFoundLockEntryException;

/**
 * Json lock file.
 */
class Lock
{
    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var array
     */
    private $packages = [];

    /**
     * Lock constructor.
     *
     * @param Filesystem $fs
     * @param object|null $json Result of the json_decode function.
     */
    public function __construct(Filesystem $fs, $json = null)
    {
        $this->fs = $fs;
        $this->packages = !is_null($json) ? (array)$json->dependencies : [];
    }

    /**
     * @param string $package
     * @param string $version
     * @throws DuplicateLockEntryException
     */
    public function add($package, $version)
    {
        if (isset($this->packages[$package])) {
            $lockedVersion = $this->packages[$package];
            throw new DuplicateLockEntryException("Package '$package' ($version) already locked with version $lockedVersion.");
        }

        $this->packages[$package] = $version;
    }

    /**
     * @param string $package
     * @throws NotFoundLockEntryException
     */
    public function remove($package)
    {
        if (!isset($this->packages[$package])) {
            throw new NotFoundLockEntryException("Cannot remove '$package' from lock file (package not found).");
        }

        unset($this->packages[$package]);
    }

    /**
     * Get installed packages versions.
     *
     * @return array Package name (key) with installed version (value).
     */
    public function installed()
    {
        return $this->packages;
    }

    /**
     * Overwrite or create new lock file.
     *
     * @param string $file
     */
    public function save($file)
    {
        $json = json_encode([
            'dependencies' => $this->installed(),
        ]);

        $this->fs->writeFile($file, $json);
    }
}
