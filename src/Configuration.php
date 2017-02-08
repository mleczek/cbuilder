<?php


namespace Mleczek\CBuilder;


/**
 * @Injectable
 */
class Configuration
{
    /**
     * @var string
     */
    private $dir;

    /**
     * Set directory containing config files.
     *
     * @param $dir
     */
    public function setDir($dir)
    {
        if (!is_dir($dir)) {
            throw new \InvalidArgumentException("Directory '$dir' not exists.");
        }

        $this->dir = $dir;
    }

    /**
     * Get value using dot notation
     * (first part is the file name).
     *
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        $parts = explode('.', $key);

        // Resolve file
        $filename = array_shift($parts) . '.php';
        $result = require $this->dir . '/' . $filename;

        // Resolve value
        foreach ($parts as $part) {
            if (!isset($result[$part])) {
                throw new \InvalidArgumentException("Cannot find '$key' configuration value.");
            }

            $result = $result[$part];
        }

        return $result;
    }
}