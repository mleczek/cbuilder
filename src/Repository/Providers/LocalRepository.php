<?php

namespace Mleczek\CBuilder\Repository\Providers;

use Mleczek\CBuilder\Downloader\Providers\LocalDownloader;
use Mleczek\CBuilder\Environment\Filesystem;
use Mleczek\CBuilder\Package\Factory as PackageFactory;
use Mleczek\CBuilder\Downloader\Factory as DownloaderFactory;
use Mleczek\CBuilder\Version\Factory as VersionFinderFactory;
use Mleczek\CBuilder\Package\Package;
use Mleczek\CBuilder\Package\Remote;
use Mleczek\CBuilder\Repository\Exceptions\PackageNotFoundException;
use Mleczek\CBuilder\Repository\Repository;
use Mleczek\CBuilder\Version\Providers\ConstVersionFinder;

/**
 * Stores information about packages in local filesystem structure.
 *
 * The root directory passed to the constructor contains the directories
 * which represents the organizations. Each organization directory contains
 * directories which represents the specified organization package.
 *
 * Each package directory must contains valid cbuilder.json file.
 *
 * Example structure:
 * - root_dir/company_name/console/...
 * - root_dir/company_name/di_container/...
 * - root_dir/org_1/package_name/...
 *   root_dir/org_1/package_name/cbuilder.json
 */
class LocalRepository implements Repository
{
    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var PackageFactory
     */
    private $packageFactory;

    /**
     * @var string
     */
    private $dir = '.';

    /**
     * @var VersionFinderFactory
     */
    private $versionFinderFactory;

    /**
     * @var DownloaderFactory
     */
    private $downloaderFactory;

    /**
     * LocalRepository constructor.
     *
     * @param Filesystem $fs
     * @param PackageFactory $packageFactory
     * @param VersionFinderFactory $versionFinderFactory
     * @param DownloaderFactory $downloaderFactory
     * @param $src
     */
    public function __construct(
        Filesystem $fs,
        PackageFactory $packageFactory,
        VersionFinderFactory $versionFinderFactory,
        DownloaderFactory $downloaderFactory,
        $src
    ) {
        $this->fs = $fs;
        $this->packageFactory = $packageFactory;
        $this->versionFinderFactory = $versionFinderFactory;
        $this->downloaderFactory = $downloaderFactory;
        $this->dir = $src;
    }

    /**
     * @param string $package
     * @return Remote
     * @throws PackageNotFoundException
     */
    public function get($package)
    {
        if (!$this->has($package)) {
            throw new PackageNotFoundException("Package '$package' not found in the local repository '{$this->dir}'.");
        }

        $path = $this->pathFor($package);

        $package = $this->packageFactory->makeFromDir($path);
        $versionFinder = $this->versionFinderFactory->makeConst();
        $downloader = $this->downloaderFactory->makeLocal($path);

        return $this->packageFactory->makeRemote(
            $this,
            $versionFinder,
            $downloader,
            $package
        );
    }

    /**
     * @param string $package
     * @return bool
     */
    public function has($package)
    {
        $dir = $this->pathFor($package);

        return $this->fs->existsDir($dir);
    }

    /**
     * Get directory for the package.
     *
     * @param string $package
     * @return string
     */
    private function pathFor($package)
    {
        return $this->fs->path($this->dir, $package);
    }

    /**
     * @return string;
     */
    public function getDir()
    {
        return $this->dir;
    }
}
