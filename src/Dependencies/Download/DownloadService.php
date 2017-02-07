<?php


namespace Mleczek\CBuilder\Dependencies\Download;


/**
 * Download package from repository with progress tracking.
 */
interface DownloadService
{
    /**
     * @param string $source
     * @return $this
     */
    public function from($source);

    /**
     * @param string $destination
     * @return $this
     */
    public function to($destination);

    /**
     * @param callable $progress First parameter indicating the progress of the operation
     *                           (int in range [0-100], where 100 means done, or -1 if error occurred).
     * @return bool
     */
    public function start(Callable $progress = null);
}