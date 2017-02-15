<?php


namespace Mleczek\CBuilder\Downloaders\Providers;

use Closure;
use Mleczek\CBuilder\Downloaders\Downloader;
use Mleczek\CBuilder\System\Filesystem;

class SymlinkDownloader implements Downloader
{
    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var string|null
     */
    private $src = null;

    /**
     * @var string|null
     */
    private $dest = null;

    /**
     * @param Filesystem $fs
     */
    public function __construct(Filesystem $fs)
    {
        $this->fs = $fs;
    }

    /**
     * @param string $src Source directory.
     * @return $this
     */
    public function from($src)
    {
        if (!is_dir($src)) {
            throw new \InvalidArgumentException("The `$src` directory cannot be found.");
        }

        $this->src = $src;
        return $this;
    }

    /**
     * @param string $dest Destination directory.
     * @return $this
     */
    public function to($dest)
    {
        $this->dest = $dest;
        return $this;
    }

    /**
     * Download source, the callback notify about downloading progress,
     * where first argument is int in range [0-100] or -1 if aborted.
     *
     * @param Closure|null $progress
     * @return bool
     */
    public function download(Closure $progress = null)
    {
        if (is_null($this->src) || is_null($this->dest)) {
            throw new \LogicException("Before downloading specify the source via 'from' method and destination via 'to' method.");
        }

        // Create symlink
        $this->fs->touchDir($this->dest);
        $isDone = symlink($this->src, $this->dest);

        // Report status/progress
        if (!is_null($progress)) {
            $progress->call($this, [$isDone ? 100 : -1]);
        }

        return $isDone;
    }
}
