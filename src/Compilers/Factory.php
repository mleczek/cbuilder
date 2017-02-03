<?php


namespace Mleczek\CBuilder\Compilers;


use Mleczek\CBuilder\Package;

class Factory
{
    /**
     * @var Manager
     */
    private $compilers;

    /**
     * Factory constructor.
     *
     * @param Manager $compilers
     */
    public function __construct(Manager $compilers)
    {
        $this->compilers = $compilers;
    }

    /**
     * Get new instance of the runner.
     *
     * @param Package $package
     * @return Runner
     */
    public function makeRunner(Package $package)
    {
        return new Runner($package, $this->compilers);
    }
}