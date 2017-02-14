<?php

namespace Mleczek\CBuilder\Repositories\Providers;

use Mleczek\CBuilder\Downloaders\Downloader;
use Mleczek\CBuilder\Downloaders\Providers\SymlinkDownloader;
use Mleczek\CBuilder\Repositories\Repository;
use Mleczek\CBuilder\Versions\Providers\ConstVersion;

class LocalRepository implements Repository
{
    /**
     * @var string
     */
    private $dir;

    /**
     * @var ConstVersion
     */
    private $versions;

    /**
     * @var SymlinkDownloader
     */
    private $downloader;

    /**
     * @param SymlinkDownloader $downloader
     * @param ConstVersion $versions
     * @param string $dir
     */
    public function __construct(SymlinkDownloader $downloader, ConstVersion $versions, $dir)
    {
        $this->dir = $dir;
        $this->versions = $versions;
        $this->downloader = $downloader;
    }

    /**
     * @param string $package
     * @param string $constraint Version constraint.
     * @return bool
     */
    public function has($package, $constraint = '*')
    {
        if (!is_dir($this->dir . '/' . $package)) {
            return false;
        }

        return $this->versions->has($constraint);
    }

    /**
     * @param string $package
     * @param string $constraint Version constraint.
     * @return bool
     */
    public function download($package, $constraint)
    {
        $src = $this->dir . '/' . $package;
        $dest = 'cmodules/' . $package; // FIXME: Read modules dir from config

        return $this->downloader
            ->from($src)->to($dest)
            ->download();
    }
}