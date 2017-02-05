<?php


namespace Mleczek\CBuilder\Compilers\Drivers;


use Mleczek\CBuilder\Compilers\Contracts\Driver;
use Mleczek\CBuilder\Compilers\Exceptions\NotSupportedException;

/**
 * Clang compiler driver.
 *
 * @link https://gcc.gnu.org/ (Linux)
 * @link http://www.mingw.org/ (Windows)
 */
class GCC extends BaseDriver
{
    /**
     * CLI options set by specified architectures.
     *
     * @var array
     */
    const ARCHITECTURE_OPTIONS = [
        'x86' => '-m32',
        'x64' => '-m64',
    ];

    /**
     * Check whether driver can be used correctly.
     *
     * @return bool
     */
    public function isSupported()
    {
        // FIXME: Return real value...

        return true;
    }

    /**
     * Get full path to the compiler executable.
     *
     * @return string
     */
    public function getPath()
    {
        // FIXME: Find path in system

        return 'M:\vendors\mingw\bin\gcc';
    }

    /**
     * Get version in "major.minor.patch" format.
     *
     * @return string
     */
    public function getVersion()
    {
        // FIXME: Get version from clang++ --version

        return '5.3.0';
    }

    /**
     * @return array
     * @throws NotSupportedException
     */
    protected function getArchOption()
    {
        $arch = $this->getArchitecture();
        if(array_key_exists($arch, self::ARCHITECTURE_OPTIONS)) {
            return [self::ARCHITECTURE_OPTIONS[$arch]];
        }

        throw new NotSupportedException(/* FIXME: Architecture not supported message */);
    }

    /**
     * @return array
     */
    protected function getDefinesOptions()
    {
        $result = [];

        // Register user-defined macros.
        foreach($this->getDefines() as $name => $value) {
            $escaped = str_replace('"', '\\"', $value);

            $result[] = '-D';
            $result[] = "$name=\"$escaped\""; // TODO: Deep test escaping characters
        }

        return $result;
    }

    /**
     * @return array
     */
    private function getDebugSymbolOption()
    {
        if(!$this->debugSymbolsFlag) {
            return [];
        }

        return ['-g'];
    }

    /**
     * @return array
     */
    private function getTempFilesOption()
    {
        if(!$this->tempFilesFlag) {
            return [];
        }

        return ['-save-temps=obj'];
    }

    /**
     * Execute the compiler and return exit code.
     *
     * @param array $sources List of source files.
     * @param null|array $output Will be filled with every line of output from the command.
     * @return int Process exit code.
     */
    public function compile(array $sources, array &$output = null)
    {
        return $this->run(array_merge(
            $sources,
            ['-o', $this->getBuildPath()],
            $this->getArchOption(),
            $this->getDefinesOptions(),
            $this->getDebugSymbolOption(),
            $this->getTempFilesOption()
        ), $output);
    }
}