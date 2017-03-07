<?php

namespace Mleczek\CBuilder\Compiler;

interface Compiler
{
    /**
     * Check whether compiler is supported
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
     * @param string $arch
     * @return $this
     */
    public function setArchitecture($arch);

    /**
     * @param string|string[] $files
     * @return $this
     */
    public function setSourceFiles($files);

    /**
     * Set directories in which compiler
     * will search for headers.
     *
     * @param string|string[] $dirs
     * @return $this
     */
    public function setIncludeDirs($dirs);

    /**
     * Register macro constraint.
     *
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function addMacro($name, $value);

    /**
     * Replace existing macros with given set.
     *
     * @param array $macros The macro name (key) with value (value).
     * @return $this
     */
    public function setMacro(array $macros);

    /**
     * @param string|string[] $libFiles
     * @return $this
     */
    public function linkStatic($libFiles);

    /**
     * @param string|string[] $libFiles
     * @return $this
     */
    public function linkDynamic($libFiles);

    /**
     * @param string $outputFile
     */
    public function buildExecutable($outputFile);

    /**
     * @param string $outputFile
     */
    public function buildStaticLibrary($outputFile);

    /**
     * @param string $outputFile
     */
    public function buildSharedLibrary($outputFile);

    /**
     * Reset to the initial configuration.
     *
     * @return $this
     */
    public function reset();
}
