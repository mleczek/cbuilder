<?php


namespace Mleczek\CBuilder\Dependencies\Repositories\Providers;


use Mleczek\CBuilder\Dependencies\Module;
use Mleczek\CBuilder\Dependencies\Repositories\Repository;

/**
 * Store information about packages in local directories structure.
 *
 * If repository path is set to "~/cbuilder" then package "org/package"
 * must be located in the "~/cbuilder/org/package" directory.
 */
class LocalRepository implements Repository
{
    /**
     * @var string
     */
    private $path;

    /**
     * @param string $path Absolute or relative path.
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * @param string $package
     * @return bool
     */
    public function has($package)
    {
        return is_dir($this->path .'/'. $package);
    }

    /**
     * @param string $package
     * @return Module
     */
    public function get($package)
    {
        if(!$this->has($package)) {
            // TODO: Throw exception...
        }

        // TODO: Implement download() method.
    }
}