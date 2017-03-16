<?php

namespace Mleczek\CBuilder\Repository;

use DI\Container;
use Mleczek\CBuilder\Environment\Config;
use Mleczek\CBuilder\Repository\Exceptions\HydratePropertyNotFoundException;
use Mleczek\CBuilder\Repository\Exceptions\UnknownRepositoryTypeException;
use Mleczek\CBuilder\Repository\Providers\EmptyRepository;
use Mleczek\CBuilder\Repository\Providers\LocalRepository;

class Factory
{
    /**
     * @var Container
     */
    private $di;

    /**
     * @var Config
     */
    private $config;

    /**
     * Factory constructor.
     *
     * @param Container $di
     * @param Config $config
     */
    public function __construct(Container $di, Config $config)
    {
        $this->di = $di;
        $this->config = $config;
    }

    /**
     * Create a collection of repositories from plain arrays.
     *
     * @param array $items
     * @return Collection
     * @throws HydratePropertyNotFoundException
     * @throws UnknownRepositoryTypeException
     */
    public function hydrate(array $items)
    {
        $collection = $this->di->make(Collection::class);

        foreach ($items as $item) {
            $item = (object)$item;

            // Check whether all required properties exists.
            if (!isset($item->type) || !isset($item->src)) {
                throw new HydratePropertyNotFoundException("The type or src key missing in repository definition.");
            }

            $repository = $this->make($item->type, $item->src);
            $collection->add($repository);
        }

        return $collection;
    }

    /**
     * Make repository with given type.
     * The types are defined in the config/repositories.php file.
     *
     * @param string $type
     * @param string $src
     * @return Repository
     * @throws UnknownRepositoryTypeException
     */
    private function make($type, $src)
    {
        if (!$this->config->has("repositories.$type")) {
            throw new UnknownRepositoryTypeException("Unrecognized '$type' repository type.");
        }

        $namespace = $this->config->get("repositories.$type");
        $repository = $this->di->make($namespace, [
            'src' => $src,
        ]);

        return $repository;
    }

    /**
     * Make local repository.
     *
     * @param string $dir
     * @return LocalRepository
     */
    public function makeLocal($dir)
    {
        $repository = $this->di->make(LocalRepository::class, [
            'src' => $dir,
        ]);

        return $repository;
    }

    /**
     * Make empty repository.
     *
     * @return EmptyRepository
     */
    public function makeEmpty()
    {
        return $this->di->make(EmptyRepository::class);
    }
}
