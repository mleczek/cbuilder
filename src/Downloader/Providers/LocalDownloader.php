<?php

namespace Mleczek\CBuilder\Downloader\Providers;

use Mleczek\CBuilder\Downloader\Downloader;
use Mleczek\CBuilder\Downloader\Exceptions\DestinationNotExistsException;
use Mleczek\CBuilder\Downloader\Exceptions\SourceNotExistsException;
use Mleczek\CBuilder\Environment\Filesystem;

class LocalDownloader implements Downloader
{
    /**
     * @var string
     */
    private $srcDir;

    /**
     * Destination directory
     *
     * @var string
     */
    private $destDir = null;

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
        $this->srcDir = $src;
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
        $this->destDir = $dir;

        return $this;
    }

    /**
     * Local downloader just point to the directory outside
     * the project directory without downloading/copying it.
     *
     * This improves development process of packages in progress
     * which still has not been published (there're always synced).
     *
     * @param string $version
     * @param \Closure|null $progress Accept one argument - percentage (int in range [0-100])
     * @return false|string Output directory if downloaded successfully, false otherwise.
     * @throws DestinationNotExistsException
     * @throws SourceNotExistsException
     */
    public function download($version, \Closure $progress = null)
    {
        $this->success = false;

        // Check source directory
        if (!$this->fs->existsDir($this->from())) {
            throw new SourceNotExistsException("Cannot point to local package '{$this->from()}' (directory not exists).");
        }

        // Check destination directory
        if (is_null($this->destDir)) {
            throw new DestinationNotExistsException("Cannot download package to '{$this->destDir}' (directory not exists).");
        }

        // Create link file
        $this->fs->writeFile("{$this->destDir}/cbuilder.link", $this->from()); // TODO: Move cbuilder.link to config

        // Inform about completion
        $this->success = true;
        if (is_callable($progress)) {
            $progress(100);
        }

        return $this->from();
    }

    /**
     * Source from which package can be download.
     *
     * @return string
     */
    public function from()
    {
        return $this->srcDir;
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
