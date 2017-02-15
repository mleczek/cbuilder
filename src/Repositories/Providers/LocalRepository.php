<?php

namespace Mleczek\CBuilder\Repositories\Providers;

use Mleczek\CBuilder\Downloaders\Downloader;
use Mleczek\CBuilder\Downloaders\Providers\SymlinkDownloader;
use Mleczek\CBuilder\Repositories\Repository;
use Mleczek\CBuilder\System\Environment;
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
     * @var Environment
     */
    private $env;

    /**
     * @param SymlinkDownloader $downloader
     * @param ConstVersion $versions
     * @param Environment $env
     * @param string $dir
     */
    public function __construct(SymlinkDownloader $downloader, ConstVersion $versions, Environment $env, $dir)
    {
        $this->dir = $dir;
        $this->versions = $versions;
        $this->downloader = $downloader;
        $this->env = $env;
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
        $dest = $this->env->config('modules.dir') . '/' . $package;

        return $this->downloader
            ->from($src)->to($dest)
            ->download();
    }
}
