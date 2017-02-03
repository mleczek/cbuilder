<?php


namespace Mleczek\CBuilder;


/**
 * Read and store cbuilder.json file data.
 */
class Package
{
    const MODULES_DIRECTORY = 'cmodules';
    const PACKAGE_FILENAME = 'cbuilder.json';

    const INCLUDE_DIR_KEY = 'include';
    const INCLUDE_DIR_DEFAULT = 'include';
    const SOURCE_DIR_KEY = 'source';
    const SOURCE_DIR_DEFAULT = 'src';
    const PACKAGE_TYPE_KEY = 'type';
    const PACKAGE_TYPE_DEFAULT = 'library';
    const ARCHITECTURE_KEY = 'arch';
    const ARCHITECTURE_DEFAULT = ['x86', 'x64'];

    /**
     * @var array
     */
    private $json;

    /**
     * Package constructor.
     *
     * @param null|string $module Module package name (null for root package).
     */
    public function __construct($module = null)
    {
        // TODO: Check if file exists
        // TODO: Check json schema

        $path = $module;

        // Search module package in modules directory.
        if (!is_null($module)) {
            $path = self::MODULES_DIRECTORY . '/' . $module . '/';
        }

        $path .= self::PACKAGE_FILENAME;
        $this->json = json_decode(file_get_contents($path));
    }

    /**
     * Get package type (library/project).
     *
     * @return string
     */
    public function getPackageType()
    {
        if (!isset($this->json->{self::PACKAGE_TYPE_KEY})) {
            return self::PACKAGE_TYPE_DEFAULT;
        }

        return (string)$this->json->{self::PACKAGE_TYPE_KEY};
    }

    /**
     * Get dir which contains headers files.
     *
     * @return string
     */
    public function getIncludeDir()
    {
        if (!isset($this->json->{self::INCLUDE_DIR_KEY})) {
            return self::INCLUDE_DIR_DEFAULT;
        }

        return (string)$this->json->{self::INCLUDE_DIR_KEY};
    }

    /**
     * Get dir which contains source files.
     *
     * @return string
     */
    public function getSourceDir()
    {
        if (!isset($this->json->{self::SOURCE_DIR_KEY})) {
            return self::SOURCE_DIR_DEFAULT;
        }

        return (string)$this->json->{self::SOURCE_DIR_KEY};
    }

    /**
     * @param string $dir
     * @param string $pattern Regex pattern (http://php.net/manual/en/book.pcre.php)
     * @param bool $recursively If true then search also in subdirectories.
     * @return array
     */
    private function listFiles($dir, $pattern = '/^.*$/iu', $recursively = true)
    {
        $results = [];

        $entries = scandir($dir, SCANDIR_SORT_NONE);
        foreach ($entries as $entry) {
            $path = $dir . '/' . $entry;

            // Omit non special entries
            // (current and parent dir).
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            // Search subdirectories if entry is a dir
            // and $recursively is set to true.
            if (is_dir($path) && $recursively) {
                $results += $this->listFiles($path);
            }

            // Add path to results if it's really a file (not symlink)
            // which name match given pattern (utf-8, case insensitive).
            if (is_file($path) && preg_match($pattern, $entry)) {
                $results[] = $path;
            }
        }

        return $results;
    }

    /**
     * Get all .cpp and .c files from source dir.
     *
     * @see getSourceDir
     * @return array
     */
    public function getSourceFiles()
    {
        $dir = $this->getSourceDir();

        return $this->listFiles($dir, '/^.*.(cpp|c)$/ui');
    }

    /**
     * Get list of supported architectures.
     *
     * @return array
     */
    public function getArchitectures()
    {
        if (!isset($this->json->{self::ARCHITECTURE_KEY})) {
            return self::ARCHITECTURE_DEFAULT;
        }

        return (array)$this->json->{self::ARCHITECTURE_KEY};
    }

    /**
     * Get current package instance.
     *
     * @see module
     * @return Package
     */
    public static function current()
    {
        return new Package();
    }

    /**
     * Get module package instance.
     *
     * @see current
     * @param $name Package name.
     * @return Package
     */
    public static function module($name)
    {
        return new Package($name);
    }
}