<?php

namespace Mleczek\CBuilder\Environment;

use Mleczek\CBuilder\Environment\Exceptions\ConfigNotExistsException;
use Mleczek\CBuilder\Environment\Exceptions\InvalidPathException;

/**
 * Configuration is stored in php files as arrays.
 */
class Config
{
    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * Config root directory.
     *
     * @var string
     */
    private $dir = '.';

    /**
     * Config constructor.
     *
     * @param Filesystem $fs
     */
    public function __construct(Filesystem $fs)
    {
        $this->fs = $fs;
    }

    /**
     * Set config root directory.
     *
     * @param string $dir
     * @throws InvalidPathException
     */
    public function setDir($dir)
    {
        if (!$this->fs->existsDir($dir)) {
            throw new InvalidPathException("Cannot set config root directory, the '$dir' directory not exists.");
        }

        $this->dir = $dir;
    }

    /**
     * Check whether config value exists,
     * the first part of the key is the file name.
     *
     * @param string $key Dot notation.
     * @return bool
     */
    public function has($key)
    {
        $parts = explode('.', $key);
        $filename = array_shift($parts);

        // Check whether config file exists.
        $file = $this->fs->path($this->dir, $filename);
        if (!$this->fs->existsFile($file)) {
            return false;
        }

        // Find config value using dot notation.
        $result = $this->fs->readFile($file);
        foreach ($parts as $part) {
            if (!isset($result[$part])) {
                return false;
            }

            $result = $result[$part];
        }

        return true;
    }

    /**
     * Get config value using dot notation,
     * where the first part is the file name.
     *
     * @param string $key
     * @return mixed
     * @throws ConfigNotExistsException
     * @throws InvalidPathException
     */
    public function get($key)
    {
        $parts = explode('.', $key);
        $filename = array_shift($parts);

        // Read config file.
        $file = $this->fs->path($this->dir, $filename);
        $result = $this->fs->readFile($file);

        // Find config value using dot notation.
        foreach ($parts as $part) {
            if (!isset($result[$part])) {
                throw new ConfigNotExistsException("Cannot find the '$part' part of a '$key' config key.");
            }

            $result = $result[$part];
        }

        return $result;
    }
}