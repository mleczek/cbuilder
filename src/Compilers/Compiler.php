<?php


namespace Mleczek\CBuilder\Compilers;


interface Compiler
{
    /**
     * Get whether compiler is supported
     * and can be used to perform compilations.
     *
     * @return bool
     */
    public function isSupported();

    /**
     * @link http://semver.org/
     * @return string Semantic version.
     */
    public function getVersion();

    /**
     * @param string|string[] $files
     * @return $this
     */
    public function setSourceFiles($files);

    /**
     * @param string $filePath
     * @return $this
     */
    public function saveOutputAs($filePath);

    /**
     * @param string $arch
     * @return $this
     */
    public function setArchitecture($arch);

    /**
     * @param bool $enabled
     * @return $this
     */
    public function withDebugSymbols($enabled = true);

    /**
     * @param bool $enabled
     * @return $this
     */
    public function withIntermediateFiles($enabled = true);

    /**
     * Register macro constraint.
     *
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function define($name, $value);

    /**
     * @param bool $static
     * @return $this
     */
    public function makeLibrary($static = false);

    /**
     * @return $this
     */
    public function makeExecutable();
}