<?php

namespace Mleczek\CBuilder\Downloader\Providers;

use Mleczek\CBuilder\Downloader\Downloader;
use Mleczek\CBuilder\Downloader\Exceptions\SourceNotExistsException;
use Mleczek\CBuilder\Environment\Filesystem;

class LocalDownloader implements Downloader
{
    /**
     * @var string
     */
    private $dir;

    /**
     * Last downloading status;
     *
     * @var bool
     */
    private $success = false;

    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * LocalDownloader constructor.
     *
     * @param Filesystem $fs
     * @param string $src
     */
    public function __construct(Filesystem $fs, $src)
    {
        $this->dir = $src;
        $this->fs = $fs;
    }

    /**
     * Set directory in which files will be stored
     * (directory will be created if not exists).
     *
     * @param string $dir
     * @return $this
     */
    public function to($dir)
    {
        // Destination directory is not required, skip it.

        return $this;
    }

    /**
     * @param string $version
     * @param \Closure|null $progress Accept one argument - percentage (int in range [0-100])
     * @return string|false Output directory if downloaded successfully, false otherwise.
     * @throws SourceNotExistsException
     */
    public function download($version, \Closure $progress = null)
    {
        $this->success = false;

        if (!$this->fs->existsDir($this->from())) {
            throw new SourceNotExistsException("Cannot point to local package '{$this->from()}' (directory not exists).");
        }

        // Local downloader just point to the directory outside
        // the project directory without downloading/copying it.
        //
        // This improves development process of packages in progress
        // which still has not been published (there're always synced).

        if (is_callable($progress)) {
            $progress(100);
        }

        $this->success = true;

        return $this->from();
    }

    /**
     * Source from which package can be download.
     *
     * @return string
     */
    public function from()
    {
        return $this->dir;
    }

    /**
     * Check whether last download completed successfully.
     *
     * @return bool
     */
    public function success()
    {
        return $this->success;
    }
}
