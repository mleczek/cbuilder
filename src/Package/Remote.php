<?php

namespace Mleczek\CBuilder\Package;

use Mleczek\CBuilder\Downloader\Downloader;
use Mleczek\CBuilder\Repository\Repository;
use Mleczek\CBuilder\Version\Finder;

/**
 * Represents package located on the repository.
 *
 * @see Package
 * @see Repository
 */
class Remote
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var Finder
     */
    private $versionFinder;

    /**
     * @var Downloader
     */
    private $downloader;

    /**
     * @var Package
     */
    private $package;

    /**
     * Remote constructor.
     *
     * @param Repository $repository
     * @param Finder $versionFinder
     * @param Downloader $downloader
     * @param Package $package
     */
    public function __construct(
        Repository $repository,
        Finder $versionFinder,
        Downloader $downloader,
        Package $package
    ) {
        $this->repository = $repository;
        $this->versionFinder = $versionFinder;
        $this->downloader = $downloader;
        $this->package = $package;
    }

    /**
     * @return Repository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @return Package
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * @return Downloader
     */
    public function getDownloader()
    {
        return $this->downloader;
    }

    /**
     * @return Finder
     */
    public function getVersionFinder()
    {
        return $this->versionFinder;
    }
}
