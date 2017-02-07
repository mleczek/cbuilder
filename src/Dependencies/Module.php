<?php


namespace Mleczek\CBuilder\Dependencies;


use Mleczek\CBuilder\Dependencies\Download\DownloadService;
use Mleczek\CBuilder\Package;

/**
 * Package installed as a part of the other package.
 * Modules are placed in the "cmodules" directory.
 */
class Module extends Package
{
    /**
     * @return DownloadService
     */
    public function download()
    {
        // TODO: ...
    }
}