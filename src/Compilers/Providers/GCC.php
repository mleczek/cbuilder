<?php

namespace Mleczek\CBuilder\Compilers\Providers;

use Mleczek\CBuilder\Compilers\Compiler;
use Mleczek\CBuilder\Compilers\Exceptions\CompilerNotFoundException;
use Mleczek\CBuilder\Compilers\Exceptions\UnknownCompilerVersionException;

/**
 * TODO: Make "-Wall" option configurable (setter)
 * TODO: Optimization level...
 *
 * @link https://gcc.gnu.org/ (Linux)
 * @link http://www.mingw.org/ (Windows)
 */
class GCC extends BaseCompiler
{
    /**
     * Console options required by the specified architectures.
     *
     * @var array
     */
    const ARCHITECTURE_OPTIONS = [
        'x86' => '-m32',
        'x64' => '-m64',
    ];

    /**
     * GCC constructor.
     */
    public function __construct()
    {
        try {
            $this->getVersion();
            $this->supported = true;
        } catch (\Exception $e) {
            $this->supported = false;
        }
    }

    /**
     * @link http://semver.org/
     * @return string Semantic version.
     * @throws CompilerNotFoundException
     * @throws UnknownCompilerVersionException
     */
    public function getVersion()
    {
        $output = [];
        $exitCode = 0;
        exec('gcc -dumpversion', $output, $exitCode);

        if ($exitCode != 0) {
            throw new CompilerNotFoundException("The gcc compiler couldn't be found. Check if the gcc is added to your path environment variables.");
        }

        $matches = [];
        if (preg_match('/^[0-9]+\.[0-9]+\.[0-9]+$/', $output[0], $matches) != 1) {
            throw new UnknownCompilerVersionException("The gcc compiler was found, but failed at establishing the compiler version. Please open the issue and attach result of the 'gcc --version', thanks!");
        }

        return $matches[0];
    }

    /**
     * @param string $arch
     * @return $this
     */
    public function setArchitecture($arch)
    {
        $available = array_keys(self::ARCHITECTURE_OPTIONS);
        if (! in_array($arch, $available)) {
            throw new \InvalidArgumentException("Architecture '$arch' isn't supported.");
        }

        parent::setArchitecture($arch);

        return $this;
    }

    /**
     * @return string[]
     */
    private function getDefineCommandOptions()
    {
        $results = [];
        foreach ($this->defines as $name => $value) {
            $value = str_replace('"', '\\"', $value);

            $results[] = '-D';
            $results[] = "$name=\"$value\"";
        }

        return $results;
    }

    /**
     * @param bool $static
     * @return $this
     */
    public function makeLibrary($static = false)
    {
        $objOutput = $this->outputPath.'.o';

        // Object file
        $this->run('gcc',
            '-c', // compile and assemble, but do not link
            '-Wall', // all warnings messages
            $this->sourceFiles,
            self::ARCHITECTURE_OPTIONS[$this->architecture],
            ['-o', $objOutput],
            $this->debugSymbols ? '-g' : [],
            $this->intermediateFiles ? '-save-temps=obj' : [],
            $this->getDefineCommandOptions()
        );

        // Library file
        if ($static) {
            $this->objToStaticLib($objOutput);
        } else {
            $this->objToSharedLib($objOutput);
        }

        return $this;
    }

    /**
     * @param string $objPath
     */
    private function objToStaticLib($objPath)
    {
        $this->run('ar',
            // "r" means to insert with replacement,
            // "c" means to create a new archive,
            // and "s" means to write an index.
            'rcs',
            $this->outputPath,
            $objPath
        );
    }

    /**
     * @param string $objPath
     */
    private function objToSharedLib($objPath)
    {
        $this->run('gcc',
            '-shared',
            ['-o', $this->outputPath],
            $objPath
        );
    }

    /**
     * @return $this
     */
    public function makeExecutable()
    {
        $this->run('gcc',
            '-Wall', // all warnings messages
            $this->sourceFiles,
            self::ARCHITECTURE_OPTIONS[$this->architecture],
            ['-o', $this->outputPath],
            $this->debugSymbols ? '-g' : [],
            $this->intermediateFiles ? '-save-temps=obj' : [],
            $this->getDefineCommandOptions()
        );

        return $this;
    }
}
