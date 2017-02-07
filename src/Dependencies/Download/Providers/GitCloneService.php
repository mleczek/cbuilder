<?php


namespace Mleczek\CBuilder\Dependencies\Download\Providers;


use Mleczek\CBuilder\Dependencies\Download\DownloadService;

/**
 * Clone local or remote git repository.
 */
class GitCloneService implements DownloadService
{
    /**
     * @param string $source
     * @return $this
     */
    public function from($source)
    {
        // TODO: Implement from() method.
    }

    /**
     * @param string $destination
     * @return $this
     */
    public function to($destination)
    {
        // TODO: Implement saveTo() method.
    }

    /**
     * @param callable $progress First parameter indicating the progress of the operation
     *                           (int in range [0-100], where 100 means done, or -1 if error occurred).
     * @return bool
     */
    public function start(Callable $progress = null)
    {
        // TODO: Implement start() method.
    }
}