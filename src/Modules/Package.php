<?php


namespace Mleczek\CBuilder\Modules;

use Mleczek\CBuilder\Modules\Exceptions\InvalidPackageFileException;
use Mleczek\CBuilder\Modules\Exceptions\PackageFileNotFoundException;
use Mleczek\CBuilder\System\Filesystem;


/**
 * The package file information.
 */
class Package
{
    const FILE_NAME = 'cbuilder.json';

    /**
     * Parsed json file.
     *
     * @var object
     */
    private $json;

    /**
     * Dir in which package file is located.
     *
     * @var string
     */
    private $dir;

    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param Factory $factory
     * @param Filesystem $filesystem
     * @param string $filePath
     */
    public function __construct(Factory $factory, Filesystem $filesystem, $filePath = self::FILE_NAME)
    {
        $this->filesystem = $filesystem;
        $this->factory = $factory;

        $this->parse($filePath);
    }

    /**
     * Read, verify and parse package file.
     *
     * @param $filePath
     * @throws InvalidPackageFileException
     * @throws PackageFileNotFoundException
     */
    private function parse($filePath)
    {
        // Try to get file content
        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new PackageFileNotFoundException("Cannot read the '$filePath' package file.");
        }

        // Check if json match the schema
        $this->dir = dirname($filePath);
        if(!$this->factory->makeValidator($this->dir)->isValid()) {
            throw new InvalidPackageFileException("The '{$this->dir}' package is corrupted.");
        }

        $this->json = json_decode($content);
    }

    /**
     * @param string $key Dot notation.
     * @param null $default
     * @return mixed
     */
    private function get($key, $default = null)
    {
        $result = $this->json;
        foreach(explode('.', $key) as $k) {
            if(!isset($result->{$k})) {
                return $default;
            }

            $result = $result->{$k};
        }

        return $result;
    }

    /**
     * Get package directory relative to the root package.
     *
     * @return string
     */
    public function getDir()
    {
        return $this->dir;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->json->name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->get('type', 'library');
    }

    /**
     * @return string
     */
    public function getIncludeDir()
    {
        return $this->get('include', 'include');
    }

    /**
     * @param string|string[] $ext
     * @return string[]
     */
    public function getIncludeFiles($ext = ['hpp', 'h'])
    {
        $ext = implode('|', (array) $ext);
        $pattern = "/^.*\\.($ext)$/u";

        $dir = $this->getDir() .'/'. $this->getIncludeDir();
        return $this->filesystem->walk($dir, $pattern);
    }

    /**
     * @return string
     */
    public function getSourceDir()
    {
        return $this->get('source', 'src');
    }

    /**
     * @param string|string[] $ext
     * @return string[]
     */
    public function getSourceFiles($ext = ['cpp', 'c'])
    {
        $ext = implode('|', (array) $ext);
        $pattern = "/^.*\\.($ext)$/u";

        $dir = $this->getDir() .'/'. $this->getSourceDir();
        return $this->filesystem->walk($dir, $pattern);
    }

    /**
     * @return string[]
     */
    public function getCompilers()
    {
        return $this->get('compiler', []);
    }

    /**
     * @return string[]
     */
    public function getArchitectures()
    {
        return $this->get('arch', ['x86', 'x64']);
    }

    /**
     * @param string $mode
     * @return string[]
     */
    public function getDefines($mode = 'release')
    {
        return $this->get("define.$mode", []);
    }
}