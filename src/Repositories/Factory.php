<?php


namespace Mleczek\CBuilder\Repositories;

use DI\Container as DIContainer;
use Mleczek\CBuilder\Repositories\Providers\LocalRepository;

class Factory
{
    /**
     * @var DIContainer
     */
    private $container;

    /**
     * @param DIContainer $container
     */
    public function __construct(DIContainer $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $dir
     * @return Repository
     */
    public function local($dir)
    {
        return $this->container->make(LocalRepository::class, [$dir]);
    }

    /**
     * Get new repository container.
     * Each module must have own container.
     *
     * @return Container
     */
    public function makeContainer()
    {
        return $this->container->make(Container::class);
    }
}
