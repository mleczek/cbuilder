<?php


namespace Mleczek\CBuilder\Configuration;


class Store
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
            // TODO: Throw exception...
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
                // TODO: Throw exception...
            }

            $result = $result[$part];
        }

        return $result;
    }
}