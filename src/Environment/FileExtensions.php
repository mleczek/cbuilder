<?php

namespace Mleczek\CBuilder\Environment;

/**
 * Get extensions specific for the current OS.
 */
class FileExtensions
{
    const EXECUTABLE = [
        'windows' => '.exe',
        'linux' => '',
    ];

    const STATIC_LIBRARY = [
        'windows' => '.lib',
        'linux' => '.a',
    ];

    const SHARED_LIBRARY = [
        'windows' => '.dll',
        'linux' => '.so',
    ];

    /**
     * Get executable extension.
     *
     * @return string
     */
    public function executable()
    {
        if ($this->isWindows()) {
            return self::EXECUTABLE['windows'];
        }

        return self::EXECUTABLE['linux'];
    }

    /**
     * Check whether current script is running
     * under the Windows environment.
     *
     * @link http://php.net/manual/en/reserved.constants.php#constant.php-os
     * @return bool
     */
    protected function isWindows()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }

    /**
     * Get static library extension.
     *
     * @return string
     */
    public function staticLibrary()
    {
        if ($this->isWindows()) {
            return self::STATIC_LIBRARY['windows'];
        }

        return self::STATIC_LIBRARY['linux'];
    }

    /**
     * Get shared library extension.
     *
     * @return string
     */
    public function sharedLibrary()
    {
        if ($this->isWindows()) {
            return self::SHARED_LIBRARY['windows'];
        }

        return self::SHARED_LIBRARY['linux'];
    }
}
