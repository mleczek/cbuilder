<?php


namespace Mleczek\CBuilder\Compilers\Contracts;


/**
 * Represents the executable compiler
 * used for building and linking libraries.
 */
interface Driver
{
    /**
     * Check whether driver can be used correctly.
     *
     * @return bool
     */
    public function isSupported();

    /**
     * Get full path to the compiler executable
     * (including file name and extension).
     *
     * @return string
     */
    public function getPath();

    /**
     * Get version in "major.minor.patch" format.
     *
     * @return string
     */
    public function getVersion();

    /**
     * @return string
     */
    public function getArchitecture();

    /**
     * @param string $arch
     * @return $this
     */
    public function setArchitecture($arch);

    /**
     * Execute the compiler and return exit code.
     *
     * @param array $sources List of source files.
     * @param null|array $output Will be filled with every line of output from the command.
     * @return int Process exit code.
     */
    public function compile(array $sources, array &$output = null);
}