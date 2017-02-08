<?php


namespace Mleczek\CBuilder\Modules;

use DI\Container;

class Factory
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @param $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param string|null $module Module name or null for root package.
     * @return Package
     */
    public function make($module = null)
    {
        if(is_null($module)) {
            return $this->container->make(Package::class);
        }

        // TODO: Get module package...
    }

    /**
     * @param string $module
     * @return Validator
     */
    public function makeValidator($module)
    {
        return $this->container->make(Validator::class, [$module]);
    }
}