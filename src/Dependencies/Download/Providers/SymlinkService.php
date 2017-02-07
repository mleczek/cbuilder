<?php


namespace Mleczek\CBuilder\Dependencies\Download\Providers;


use Mleczek\CBuilder\Dependencies\Download\DownloadService;

/**
 * Create symbolic link to the package directory.
 */
class SymlinkService implements DownloadService
{
    /**
     * @var string
     */
    private $source;

    /**
     * @var string
     */
    private $destination;

    /**
     * @param string $source
     * @return $this
     */
    public function from($source)
    {
        $this->source = $source;
        return $this;
    }

    /**
     * @param string $destination
     * @return $this
     */
    public function to($destination)
    {
        $this->destination = $destination;
        return $this;
    }

    /**
     * @param callable $progress First parameter indicating the progress of the operation
     *                           (int in range [0-100], where 100 means done, or -1 if error occurred).
     * @return bool
     */
    public function start(Callable $progress = null)
    {
        if(symlink($this->destination, $this->source)) {
            return $progress(100);
        }

        return $progress(-1);
    }
}