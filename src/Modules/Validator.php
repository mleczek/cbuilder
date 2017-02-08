<?php


namespace Mleczek\CBuilder\Modules;


/**
 * Check module correctness.
 */
class Validator
{
    /**
     * @var string
     */
    private $dir;

    /**
     * @param string $dir
     */
    public function __construct($dir = '.')
    {
        $this->dir = $dir;
    }

    /**
     * Check whether all aspects of the module
     * are valid and ready to use.
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->isPackageFileValid()
            && $this->isPackageLockFileValid();
    }

    /**
     * @return bool
     */
    public function isPackageFileValid()
    {
        // TODO: ...
        return true;
    }

    /**
     * @return bool
     */
    public function isPackageLockFileValid()
    {
        // TODO: ...
        return true;
    }
}