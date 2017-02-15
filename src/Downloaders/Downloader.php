<?php


namespace Mleczek\CBuilder\Downloaders;

use Closure;

interface Downloader
{
    /**
     * @param string $src
     * @return $this
     */
    public function from($src);

    /**
     * @param string $dest
     * @return $this
     */
    public function to($dest);

    /**
     * Download source, the callback notify about downloading progress,
     * where first argument is int in range [0-100] or -1 if aborted.
     *
     * @param Closure $progress
     */
    public function download(Closure $progress);
}
