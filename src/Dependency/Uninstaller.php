<?php

namespace Mleczek\CBuilder\Dependency;

use Mleczek\CBuilder\Environment\Config;
use Mleczek\CBuilder\Environment\Filesystem;
use Mleczek\CBuilder\Lock\Factory;

/**
 * Remove specified (or all) dependencies.
 * Fails if trying to remove not installed package.
 */
class Uninstaller
{
    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Factory
     */
    private $lockFactory;

    /**
     * Uninstaller constructor.
     *
     * @param Filesystem $fs
     * @param Config $config
     * @param Factory $lockFactory
     */
    public function __construct(Filesystem $fs, Config $config, Factory $lockFactory)
    {
        $this->fs = $fs;
        $this->config = $config;
        $this->lockFactory = $lockFactory;
    }

    /**
     * @param string[] $packages Packages names.
     */
    public function uninstall($packages)
    {
        $modulesDir = $this->config->get('modules.output_dir');
        $metaDir = $this->config->get('modules.meta_dir');
        $installedFile = $this->config->get('modules.installed_lock');

        // Load lock file (or create new).
        $lockPath = $this->fs->path($modulesDir, $metaDir, $installedFile);
        $lock = $this->lockFactory->makeFromFileOrEmpty($lockPath);

        // Remove packages directories
        // (as well as entry from lock file).
        foreach ($packages as $packageName) {
            $packageDir = $this->fs->path($modulesDir, $packageName);
            $this->fs->removeDir($packageDir);
            $lock->remove($packageName);
        }

        // Update installed lock file.
        $lock->save($lockPath);
    }

    public function uninstallAll()
    {
        $modulesDir = $this->config->get('modules.output_dir');
        $this->fs->removeDir($modulesDir);
    }
}
