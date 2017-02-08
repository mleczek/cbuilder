<?php


namespace Mleczek\CBuilder\System;


/**
 * Environment variables, including config
 * and system/console information.
 */
class Environment
{
    /**
     * @var string
     */
    private $configDir;

    /**
     * @param string $dir
     */
    public function setConfigDir($dir)
    {
        $this->configDir = $dir;
    }

    /**
     * Get config value, first part
     * of the key is the file name.
     *
     * @param string $key Dot notation.
     * @return mixed
     */
    public function config($key)
    {
        $keys = explode('.', $key);
        $filename = array_shift($keys);

        $result = require $this->configDir . '/' . $filename .'.php';
        foreach($keys as $k) {
            if(!isset($result[$k])) {
                throw new \InvalidArgumentException("Cannot find config value for the '$key' key.");
            }

            $result = $result[$k];
        }

        return $result;
    }
}