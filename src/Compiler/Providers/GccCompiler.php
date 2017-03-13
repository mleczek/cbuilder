<?php

namespace Mleczek\CBuilder\Compiler\Providers;

use Mleczek\CBuilder\Compiler\Exceptions\CompilerNotFoundException;
use Mleczek\CBuilder\Compiler\Exceptions\UnknownCompilerVersionException;
use Mleczek\CBuilder\Environment\Filesystem;

/**
 * @link https://gcc.gnu.org/ (Linux)
 * @link http://www.mingw.org/ (Windows)
 */
class GccCompiler extends BaseCompiler
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
     * @var Filesystem
     */
    private $fs;

    /**
     * GccCompiler constructor.
     *
     * @param Filesystem $fs
     */
    public function __construct(Filesystem $fs)
    {
        $this->fs = $fs;
        $this->preapre();
    }

    /**
     * Search gcc in system and set
     * required flags and version number.
     */
    private function preapre()
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
     * @throws \InvalidArgumentException
     */
    public function setArchitecture($arch)
    {
        $available = array_keys(self::ARCHITECTURE_OPTIONS);
        if (!in_array($arch, $available)) {
            throw new \InvalidArgumentException("Architecture '$arch' isn't supported.");
        }

        parent::setArchitecture($arch);

        return $this;
    }

    /**
     * @param string $outputFile
     */
    public function buildExecutable($outputFile)
    {
        $this->run('gcc',
            '-Wall', // all warnings messages
            $this->sourceFiles,
            self::ARCHITECTURE_OPTIONS[$this->architecture],
            ['-o', $outputFile],
            $this->debugSymbols ? '-g' : [],
            $this->intermediateFiles ? '-save-temps=obj' : [],
            $this->getMacrosCommandOptions(),
            $this->getLibrariesCommandOptions(),
            $this->getIncludeDirsCommandOptions()
        );
    }

    /**
     * Get macro defines gcc options.
     *
     * @link https://gcc.gnu.org/onlinedocs/gcc/Preprocessor-Options.html#Preprocessor-Options
     * @return array
     */
    private function getMacrosCommandOptions()
    {
        $results = [];
        foreach ($this->macros as $name => $value) {
            $value = str_replace('"', '\"', $value);

            $results[] = '-D';
            $results[] = "$name=\"$value\"";
        }

        return $results;
    }

    /**
     * Get static and shared libraries gcc options.
     *
     * @link https://gcc.gnu.org/onlinedocs/gcc/Link-Options.html#Link-Options
     * @return array
     */
    private function getLibrariesCommandOptions()
    {
        $results = [];
        $libraries = array_merge(
            $this->linkStatic,
            $this->linkDynamic
        );

        foreach ($libraries as $libPath) {
            $libName = $this->fs->getFileName($libPath);
            $libDir = $this->fs->getDirName($libPath);

            if (!empty($libDir)) {
                $results[] = '-L';
                $results[] = escapeshellarg($libDir);
            }

            $results[] = '-l';
            $results[] = escapeshellarg($libName);
        }

        return [];
    }

    /**
     * Get include dirs gcc options.
     *
     * @link https://gcc.gnu.org/onlinedocs/gcc/Directory-Options.html#Directory-Options
     * @return array
     */
    private function getIncludeDirsCommandOptions()
    {
        $results = [];
        foreach ($this->includeDirs as $dir) {
            $results[] = '-I';
            $results[] = escapeshellarg($dir);
        }

        return $results;
    }

    /**
     * @param string $outputFile
     */
    public function buildStaticLibrary($outputFile)
    {
        // TODO: Implement buildStaticLibrary() method.
    }

    /**
     * @param string $outputFile
     */
    public function buildSharedLibrary($outputFile)
    {
        // TODO: Implement buildSharedLibrary() method.
    }
}
