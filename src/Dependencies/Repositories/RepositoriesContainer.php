<?php


namespace Mleczek\CBuilder\Dependencies\Repositories;

use Mleczek\CBuilder\Dependencies\Module;


/**
 * Store information about available repositories
 * and provide methods to search through the container.
 *
 * @Injectable
 */
class RepositoriesContainer
{
    /**
     * @var int
     */
    const HIGHEST_PRIORITY = 0;

    /**
     * Must be greater or equal the HIGHEST_PRIORITY constant.
     *
     * @var int
     */
    const LOWEST_PRIORITY = 10;

    /**
     * Repositories grouped by the priority.
     *
     * @var Repository[][]
     */
    private $container = [];

    /**
     * @param Repository $repo
     * @param int $priority In range [0, 10], where 0 is the highest priority and 10 is the lowest priority.
     */
    public function add(Repository $repo, $priority = self::LOWEST_PRIORITY)
    {
        if ($priority < self::HIGHEST_PRIORITY || $priority > self::LOWEST_PRIORITY) {
            throw new \OutOfRangeException('Priority must be between ' . self::LOWEST_PRIORITY . ' and ' . self::HIGHEST_PRIORITY . ' (inclusive).');
        }

        $this->container[$priority][] = $repo;
    }

    /**
     * Search given package in repositories,
     * sorted from highest to lowest priority.
     *
     * @param string $package
     * @return Module|null Module or null if module is not found.
     */
    public function search($package)
    {
        // Iterate from highest to lowest priority
        for ($i = self::HIGHEST_PRIORITY; $i <= self::LOWEST_PRIORITY; ++$i) {
            // Skip if no repositories were added for this priority
            if(!isset($this->container[$i])) {
                continue;
            }

            // Iterate in order in which they were added
            foreach($this->container[$i] as $repo) {
                if($repo->has($package)) {
                    return $repo->get($package);
                }
            }
        }

        return null;
    }
}