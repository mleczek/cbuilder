<?php


namespace Mleczek\CBuilder\Compilers;


use DI\Container;
use Mleczek\CBuilder\Package;

/**
 * @Injectable
 */
class Factory
{
    /**
     * @Inject
     * @var CompilersContainer
     */
    private $compilers;

    /**
     * @Inject
     * @var Container
     */
    private $container;

    /**
     * Get new instance of the runner.
     *
     * @param Package $package
     * @return ArtifactsBuilder
     */
    public function makeBuilderFor(Package $package)
    {
        return $this->container->make(ArtifactsBuilder::class, [$package]);
    }
}