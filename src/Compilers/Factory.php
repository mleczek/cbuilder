<?php


namespace Mleczek\CBuilder\Compilers;


use Mleczek\CBuilder\Package;

class Factory
{
    /**
     * @var CompilersContainer
     */
    private $compilers;

    /**
     * Factory constructor.
     *
     * @param CompilersContainer $compilers
     */
    public function __construct(CompilersContainer $compilers)
    {
        $this->compilers = $compilers;
    }

    /**
     * Get new instance of the runner.
     *
     * @param Package $package
     * @return ArtifactsBuilder
     */
    public function makeRunner(Package $package)
    {
        return new ArtifactsBuilder($package, $this->compilers);
    }
}