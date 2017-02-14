<?php


namespace Mleczek\CBuilder\Repositories;


use DI\Container;
use Mleczek\CBuilder\Repositories\Providers\LocalRepository;

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
     * @param string $dir
     * @return Repository
     */
    public function local($dir)
    {
        return $this->container->make(LocalRepository::class, [$dir]);
    }
}