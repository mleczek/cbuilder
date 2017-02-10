<?php


namespace Mleczek\CBuilder\Compilers\Providers;


use Mleczek\CBuilder\Compilers\Compiler;

abstract class BaseCompiler implements Compiler
{
    /**
     * @var bool
     */
    protected $supported = false;

    /**
     * @var string[]
     */
    protected $sourceFiles = [];

    /**
     * @var string
     */
    protected $outputPath;

    /**
     * @var string
     */
    protected $architecture;

    /**
     * @var bool
     */
    protected $debugSymbols;

    /**
     * @var bool
     */
    protected $intermediateFiles;

    /**
     * @var string[]
     */
    protected $defines = [];

    /**
     * Get whether compiler is supported
     * and can be used to perform compilations.
     *
     * @return bool
     */
    public function isSupported()
    {
        return $this->supported;
    }

    /**
     * @param string|string[] $files
     * @return $this
     */
    public function setSourceFiles($files)
    {
        $this->sourceFiles = (array)$files;
        return $this;
    }

    /**
     * @param string $filePath Output file name (dir must exists).
     * @return $this
     */
    public function saveOutputAs($filePath)
    {
        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            throw new \InvalidArgumentException("Directory '$dir' not exists.");
        }

        $this->outputPath = $filePath;
        return $this;
    }

    /**
     * @param string $arch
     * @return $this
     */
    public function setArchitecture($arch)
    {
        $this->architecture = $arch;
        return $this;
    }

    /**
     * @param bool $enabled
     * @return $this
     */
    public function withDebugSymbols($enabled = true)
    {
        $this->debugSymbols = $enabled;
        return $this;
    }

    /**
     * @param bool $enabled
     * @return $this
     */
    public function withIntermediateFiles($enabled = true)
    {
        $this->intermediateFiles = $enabled;
        return $this;
    }

    /**
     * Register macro constraint.
     *
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function define($name, $value)
    {
        if (preg_match('/^[A-Z_]+$/', $name) == 0) {
            throw new \InvalidArgumentException("The macro name '$name' can contain only uppercase chars (A-Z) and underscore symbol.");
        }

        $this->defines[$name] = $value;
        return $this;
    }

    /**
     * @param mixed $args,... Arguments combined with space.
     */
    protected function run(...$args)
    {
        $arrToString = function ($e) {
            return is_array($e) ? implode(' ', $e) : $e;
        };

        $command = implode(' ', array_map($arrToString, $args));
        exec($command);
    }
}