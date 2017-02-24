<?php

namespace Mleczek\CBuilder\Downloader;

interface Downloader
{
    /**
     * Source from which package can be download.
     *
     * @return string
     */
    public function from();

    /**
     * Set directory in which files will be stored
     * (directory will be created if not exists).
     *
     * @param string $dir
     * @return $this
     */
    public function to($dir);

    /**
     * @param string $version
     * @param \Closure|null $progress Accept one argument - percentage (int in range [0-100])
     * @return string|false Output directory if downloaded successfully, false otherwise.
     */
    public function download($version, \Closure $progress = null);

    /**
     * Check whether last download completed successfully.
     *
     * @return bool
     */
    public function success();
}
