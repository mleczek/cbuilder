<?php

namespace Mleczek\CBuilder\Repository\Providers;

use Mleczek\CBuilder\Environment\Filesystem;
use Mleczek\CBuilder\Package\Factory;
use Mleczek\CBuilder\Package\Package;
use Mleczek\CBuilder\Package\Remote;
use Mleczek\CBuilder\Repository\Exceptions\PackageNotFoundException;
use Mleczek\CBuilder\Repository\Repository;
use Mleczek\CBuilder\Version\Providers\ConstVersionResolver;

/**
 * Stores information about packages in local filesystem structure.
 *
 * The root directory passed to the constructor contains the directories
 * which represents the organizations. Each organization directory contains
 * directories which represents the specified organization package.
 *
 * Each package directory must contains valid cbuilder.json file.
 *
 * Example structure:
 * - root_dir/company_name/console/...
 * - root_dir/company_name/di_container/...
 * - root_dir/org_1/package_name/...
 *   root_dir/org_1/package_name/cbuilder.json
 */
class LocalRepository implements Repository
{
    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var string
     */
    private $dir = '.';

    /**
     * LocalRepository constructor.
     *
     * @param Filesystem $fs
     * @param Factory $factory
     * @param $src
     */
    public function __construct(
        Filesystem $fs,
        Factory $factory,
        $src
    ) {
        $this->fs = $fs;
        $this->factory = $factory;
        $this->dir = $src;
    }

    /**
     * @param string $package
     * @return Remote
     * @throws PackageNotFoundException
     */
    public function get($package)
    {
        if (!$this->has($package)) {
            throw new PackageNotFoundException("Package '$package' not found in the local repository '{$this->dir}'.");
        }

        $package = $this->factory->makeFromDir(
            $this->pathFor($package)
        );

        return $this->factory->makeRemote($this, $package);
    }

    /**
     * @param string $package
     * @return bool
     */
    public function has($package)
    {
        $dir = $this->pathFor($package);

        return $this->fs->existsDir($dir);
    }

    /**
     * Get directory for the package.
     *
     * @param string $package
     * @return string
     */
    private function pathFor($package)
    {
        return $this->fs->path($this->dir, $package);
    }

    /**
     * @return string;
     */
    public function getDir()
    {
        return $this->dir;
    }
}
