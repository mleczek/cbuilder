<?php


namespace Mleczek\CBuilder\Console\Tools;


use DI\Container;
use Mleczek\CBuilder\Modules\Package;

class Factory
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param Package $package
     * @return ArtifactsBuilder
     */
    public function makeArtifactsBuilder(Package $package)
    {
        return $this->container->make(ArtifactsBuilder::class, [$package]);
    }
}