<?php

namespace Mleczek\CBuilder\Package;

use Mleczek\CBuilder\Repository\Repository;

/**
 * Represents package located on the repository.
 *
 * @see Package
 * @see Repository
 */
class Remote
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var Package
     */
    private $package;

    /**
     * Remote constructor.
     *
     * @param Repository $repository
     * @param Package $package
     */
    public function __construct(Repository $repository, Package $package)
    {
        $this->repository = $repository;
        $this->package = $package;
    }

    /**
     * @return Repository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @return Package
     */
    public function getPackage()
    {
        return $this->package;
    }
}
