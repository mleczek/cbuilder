<?php


namespace Mleczek\CBuilder\Compilers\Providers;


use Mleczek\CBuilder\Compilers\Compiler;
use Mleczek\CBuilder\Compilers\Exceptions\CompilerNotFoundException;
use Mleczek\CBuilder\Compilers\Exceptions\UnknownCompilerVersionException;

/**
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
        exec('gcc --version', $output, $exitCode);

        if ($exitCode != 0) {
            throw new CompilerNotFoundException("The gcc compiler couldn't be found. Check if the gcc is added to your path environment variables.");
        }

        $matches = [];
        if (preg_match('/[0-9]+\.[0-9]+\.[0-9]+/', $output[0], $matches) != 1) {
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
        if(!in_array($arch, $available)) {
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
        foreach($this->defines as $name => $value) {
            $value = str_replace('"', '\\"', $value);

            $results[] = '-D';
            $results[] = "$name=\"$value\"";
        }

        return $results;
    }

    /**
     * Build artifacts.
     */
    public function compile()
    {
        $this->run('gcc',
            $this->sourceFiles,
            self::ARCHITECTURE_OPTIONS[$this->architecture],
            ['-o', $this->outputPath],
            $this->debugSymbols ? '-g' : [],
            $this->intermediateFiles ? '-save-temps=obj' : [],
            $this->getDefineCommandOptions()
        );
    }
}