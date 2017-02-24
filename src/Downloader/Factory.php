<?php

namespace Mleczek\CBuilder\Downloader;

use DI\Container;
use Mleczek\CBuilder\Downloader\Providers\LocalDownloader;

class Factory
{
    /**
     * @var Container
     */
    private $di;

    /**
     * Factory constructor.
     *
     * @param Container $di
     */
    public function __construct(Container $di)
    {
        $this->di = $di;
    }

    /**
     * @return LocalDownloader
     */
    public function makeLocal($dir)
    {
        return $this->di->make(LocalDownloader::class, [
            'src' => $dir,
        ]);
    }
}
