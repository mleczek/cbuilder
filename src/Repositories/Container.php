<?php


namespace Mleczek\CBuilder\Repositories;


use Mleczek\CBuilder\Repositories\Exceptions\RepositoryNotFoundException;

class Container
{
    /**
     * @var int
     */
    const HIGHEST_PRIORITY = 0;

    /**
     * Must be equal or greater than
     * the HIGHEST_PRIORITY constant.
     *
     * @var int
     */
    const LOWEST_PRIORITY = 10;

    /**
     * The first key is the priority
     * in range [HIGHEST_PRIORITY, LOWEST_PRIORITY].
     *
     * @var Repository[][]
     */
    private $repositories = [];

    /**
     * @param Repository $repository
     * @param int $priority In range [HIGHEST_PRIORITY, LOWEST_PRIORITY].
     */
    public function register(Repository $repository, $priority)
    {
        if ($priority < self::HIGHEST_PRIORITY || $priority > self::LOWEST_PRIORITY) {
            throw new \InvalidArgumentException("Priority must be between " . self::HIGHEST_PRIORITY . " and " . self::LOWEST_PRIORITY . " (inclusive).");
        }

        $this->repositories[$priority][] = $repository;
    }

    /**
     * Find repository which contains given package
     * (from highest to lowest repositories priority).
     *
     * @param string $package
     * @return Repository
     * @throws RepositoryNotFoundException
     */
    public function find($package)
    {
        // Search in repositories from the highest to the lowest priority
        for($i = self::HIGHEST_PRIORITY; $i <= self::LOWEST_PRIORITY; ++$i) {
            // If any repository with given priority exists...
            if(isset($this->repositories[$i])) {
                // Iterate over the collection of them...
                foreach($this->repositories[$i] as $repo) {
                    // And return repository if contains the package
                    if($repo->has($package)) {
                        return $repo;
                    }
                }
            }
        }

        throw new RepositoryNotFoundException("Cannot find the '$package' package in registered repositories.");
    }
}