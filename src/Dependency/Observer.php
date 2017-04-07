<?php

namespace Mleczek\CBuilder\Dependency;

use Mleczek\CBuilder\Environment\Config;
use Mleczek\CBuilder\Environment\Filesystem;
use Mleczek\CBuilder\Lock\Factory;

/**
 * List installed dependencies from structure in cmodules directory
 * (their versions are stored in installed.lock file inside cmodules).
 */
class Observer
{
    /**
     * @var string[] Package name (key) with version (value).
     */
    protected $installed = [];

    /**
     * @var string[] Package name (value).
     */
    protected $ambiguous = [];

    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var Config
     */
    private $config;

    /**
     * Observer constructor.
     *
     * @param Filesystem $fs
     * @param Factory $factory
     * @param Config $config
     */
    public function __construct(Filesystem $fs, Factory $factory, Config $config)
    {
        $this->fs = $fs;
        $this->factory = $factory;
        $this->config = $config;
    }

    /**
     * @return array Package name (key) with installed version (value).
     */
    private function listLockedPackages()
    {
        $installedLockDir = $this->config->get('modules.output_dir') .'/'. $this->config->get('modules.meta_dir');
        $installed = $this->factory->makeFromFileOrEmpty("$installedLockDir/installed.lock"); // TODO: Move installed.lock to config

        return $installed->packages();
    }

    /**
     * @return string[] Packages names.
     */
    private function listDownloadedPackages()
    {
        $outputDir = $this->config->get('modules.output_dir');
        $dir2package = function($dir) use ($outputDir) {
            return substr($dir, strlen("$outputDir/"));
        };

        $cmodules = $this->fs->listDirs($outputDir, 2);
        $packages = array_map($dir2package, $cmodules);

        return $packages;
    }

    /**
     * Make snapshot of the installed packages.
     *
     * @return $this
     */
    public function observe()
    {
        $locked = $this->listLockedPackages();
        $downloaded = $this->listDownloadedPackages();

        // Detect ambiguous packages.
        $installedPackagesNames = array_keys($locked);
        $this->ambiguous = array_merge(
            array_diff($installedPackagesNames, $downloaded), // Marked as installed, but not exists in dir structure.
            array_diff($downloaded, $installedPackagesNames) // Installed, but not contains information about version.
        );

        // Mark other packages as installed.
        $this->installed = array_filter($locked, function($value, $key) use ($downloaded) {
            return array_search($key, $downloaded) !== false;
        }, ARRAY_FILTER_USE_BOTH);

        return $this;
    }

    /**
     * Get names of packages which are:
     * - installed, but version cannot be determined,
     * - or not installed, but listed as an installed ones.
     *
     * @return string[]
     */
    public function getAmbiguous()
    {
        return $this->ambiguous;
    }

    /**
     * Get installed dependencies list.
     *
     * @return string[] Package name (key) with version (value).
     */
    public function getInstalled()
    {
        return $this->installed;
    }
}
