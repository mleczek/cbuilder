<?php


namespace Mleczek\CBuilder\Console\Tools;


use Mleczek\CBuilder\Modules\Package;

class PathResolver
{
    /**
     * @return string
     */
    public function getPackageFileName()
    {
        return Package::FILE_NAME;
    }

    /**
     * @param string $dir
     * @return string
     */
    public function getPackageFilePath($dir)
    {
        return $dir .'/'. $this->getPackageFileName();
    }

    /**
     * @return string
     */
    public function getModulesDir()
    {
        return 'cmodules';
    }

    /**
     * @param string $module
     * @return string
     */
    public function getModuleDir($module)
    {
        return $this->getModulesDir() .'/'. $module;
    }
}