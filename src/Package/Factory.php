<?php

namespace Mleczek\CBuilder\Package;

use DI\Container;
use Mleczek\CBuilder\Downloader\Downloader;
use Mleczek\CBuilder\Environment\Config;
use Mleczek\CBuilder\Environment\Exceptions\InvalidPathException;
use Mleczek\CBuilder\Environment\Filesystem;
use Mleczek\CBuilder\Repository\Repository;
use Mleczek\CBuilder\Validation\Exceptions\ValidationException;
use Mleczek\CBuilder\Validation\Validator;
use Mleczek\CBuilder\Version\Finder;

class Factory
{
    /**
     * @var Container
     */
    private $di;

    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * Factory constructor.
     *
     * @param Container $di
     * @param Filesystem $fs
     * @param Config $config
     * @param Validator $validator
     */
    public function __construct(Container $di, Filesystem $fs, Config $config, Validator $validator)
    {
        $this->di = $di;
        $this->fs = $fs;
        $this->config = $config;
        $this->validator = $validator;
    }

    /**
     * @return Package
     * @throws InvalidPathException
     */
    public function makeCurrent()
    {
        try {
            return $this->makeFromDir('.');
        } catch (\Exception $e) {
            throw new InvalidPathException("Working directory is not recognized as the cbuilder package, check path or create new package using 'cbuilder init' command.");
        }
    }

    /**
     * @param string $dir
     * @return Package
     */
    public function makeFromDir($dir)
    {
        $file = $this->fs->path($dir, $this->config->get('package.filename'));

        return $this->makeFromFile($file);
    }

    /**
     * @param string $file
     * @return Package
     * @throws InvalidPathException
     */
    public function makeFromFile($file)
    {
        $content = $this->fs->readFile($file);

        return $this->makeFromJson($content);
    }

    /**
     * @param string|object $json
     * @return Package
     * @throws \Mleczek\CBuilder\Validation\Exceptions\ValidationException
     */
    public function makeFromJson($json)
    {
        $this->validator->validate(
            $json,
            $this->fs->readFile(CBUILDER_DIR . '/resources/package.schema.json')
        );

        if (!is_object($json)) {
            $json = json_decode($json);
        }

        return $this->di->make(Package::class, [
            'json' => $json,
        ]);
    }

    /**
     * @param Repository $repository
     * @param Finder $versionFinder
     * @param Downloader $downloader
     * @param Package $package
     * @return Remote
     */
    public function makeRemote(
        Repository $repository,
        Finder $versionFinder,
        Downloader $downloader,
        Package $package
    ) {
        return $this->di->make(Remote::class, [
            'repository' => $repository,
            'versionFinder' => $versionFinder,
            'downloader' => $downloader,
            'package' => $package,
        ]);
    }
}
