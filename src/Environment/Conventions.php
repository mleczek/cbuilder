<?php

namespace Mleczek\CBuilder\Environment;

/**
 * Get executables/libraries paths/extensions
 * specific for the current operating system.
 */
class Conventions
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

    const LIBRARY_PREFIX = [
        'windows' => '',
        'linux' => 'lib',
    ];

    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * FileNameResolver constructor.
     *
     * @param Filesystem $fs
     */
    public function __construct(Filesystem $fs)
    {
        $this->fs = $fs;
    }

    /**
     * Get static library extension.
     *
     * @return string
     */
    public function getStaticLibExt()
    {
        if ($this->isWindows()) {
            return self::STATIC_LIBRARY['windows'];
        }

        return self::STATIC_LIBRARY['linux'];
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

    public function getExePath($outputFile)
    {
        return $outputFile . $this->getExeExt();
    }

    /**
     * Get executable extension.
     *
     * @return string
     */
    public function getExeExt()
    {
        if ($this->isWindows()) {
            return self::EXECUTABLE['windows'];
        }

        return self::EXECUTABLE['linux'];
    }

    /**
     * @param string $libFile
     * @return string
     */
    public function toSharedLibPath($libFile)
    {
        $dir = $this->fs->getDirName($libFile);

        $prefix = $this->getLibPrefix();
        $file = $this->fs->getFileName($libFile);
        $ext = $this->toSharedLibExt();

        return $this->fs->path($dir, $prefix . $file . $ext);
    }

    /**
     * @return string
     */
    public function getLibPrefix()
    {
        if ($this->isWindows()) {
            return self::LIBRARY_PREFIX['windows'];
        }

        return self::LIBRARY_PREFIX['linux'];
    }

    /**
     * Get shared library extension.
     *
     * @return string
     */
    public function toSharedLibExt()
    {
        if ($this->isWindows()) {
            return self::SHARED_LIBRARY['windows'];
        }

        return self::SHARED_LIBRARY['linux'];
    }

    /**
     * @param string $libFile
     * @return string
     */
    public function toStaticLibPath($libFile)
    {
        $dir = $this->fs->getDirName($libFile);

        $prefix = $this->getLibPrefix();
        $file = $this->fs->getFileName($libFile);
        $ext = $this->getStaticLibExt();

        return $this->fs->path($dir, $prefix . $file . $ext);
    }
}
