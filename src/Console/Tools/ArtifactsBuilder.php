<?php

namespace Mleczek\CBuilder\Console\Tools;

use Mleczek\CBuilder\Modules\Package;
use Mleczek\CBuilder\System\Filesystem;
use Mleczek\CBuilder\Compilers\Compiler;
use Mleczek\CBuilder\Compilers\Container;
use Mleczek\CBuilder\Versions\Comparator;
use Mleczek\CBuilder\Compilers\Exceptions\CompilerNotFoundException;

/**
 * Perform linking and compiling process of the package.
 */
class ArtifactsBuilder
{
    /**
     * @var Package
     */
    private $package;

    /**
     * @var bool
     */
    private $debugMode = false;

    /**
     * @var string|null
     */
    private $compiler = null;

    /**
     * @var string[]
     */
    private $architectures = [];

    /**
     * @var PathResolver
     */
    private $path;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Container
     */
    private $compilers;

    /**
     * @var Comparator
     */
    private $versions;

    /**
     * @param PathResolver $path
     * @param Filesystem $fs
     * @param Container $compilers
     * @param Package $package
     */
    public function __construct(PathResolver $path, Filesystem $fs, Container $compilers, Package $package, Comparator $versions)
    {
        $this->path = $path;
        $this->filesystem = $fs;
        $this->compilers = $compilers;
        $this->package = $package;
        $this->versions = $versions;
    }

    /**
     * @param bool $enabled
     * @return $this
     */
    public function useDebugMode($enabled = true)
    {
        $this->debugMode = $enabled;

        return $this;
    }

    /**
     * @param string|null $compilerName
     * @return $this
     */
    public function setCompiler($compilerName)
    {
        $this->compiler = $compilerName;

        return $this;
    }

    /**
     * Get compiler set by setter
     * or best suitable from the package file.
     *
     * @return Compiler
     * @throws CompilerNotFoundException
     */
    private function getCompiler()
    {
        // If set via cli then use specified compiler
        if (! is_null($this->compiler)) {
            return $this->compilers->get($this->compiler);
        }

        // Or find preferred in package configuration
        if (! empty($this->package->getCompilers())) {
            foreach ($this->package->getCompilers() as $name => $constraint) {
                // Check whether compiler with given name was registered
                if ($this->compilers->has($name)) {
                    $compiler = $this->compilers->get($name);

                    // If compiler match given version constraint
                    if ($this->versions->satisfies($compiler->getVersion(), $constraint)) {
                        return $compiler;
                    }
                }
            }

            throw new CompilerNotFoundException('Cannot find any of the compilers defined in the package file.');
        }

        // Or get any of the available compilers
        return $this->compilers->getOne();
    }

    /**
     * @param string|string[] $arch
     * @return $this
     */
    public function setArchitectures($arch)
    {
        $this->architectures = (array) $arch;

        return $this;
    }

    /**
     * Get architectures set by setter
     * or these defined in the package file.
     *
     * @return string[]
     */
    private function getArchitectures()
    {
        if (! empty($this->architectures)) {
            return $this->architectures;
        }

        return $this->package->getArchitectures();
    }

    /**
     * Build artifacts.
     */
    public function build()
    {
        $compiler = $this->getCompiler();
        $architectures = $this->getArchitectures();

        // Register macros
        $buildMode = $this->debugMode ? 'debug' : 'release';
        foreach ($this->package->getDefines($buildMode) as $name => $value) {
            $compiler->define($name, $value);
        }

        // Build artifacts for each architecture
        foreach ($architectures as $arch) {
            $compiler->setArchitecture($arch)
                ->setSourceFiles($this->package->getSourceFiles())
                ->withDebugSymbols($this->debugMode)
                ->withIntermediateFiles($this->debugMode);

            $path = $this->path->getOutputDir($arch);
            $this->filesystem->touchDir($path);

            if ($this->package->getType() == 'library') {
                $path = $this->path->getLibraryPath($this->package, $arch, true);
                $compiler->saveOutputAs($path)->makeLibrary(true);

                $path = $this->path->getLibraryPath($this->package, $arch, false);
                $compiler->saveOutputAs($path)->makeLibrary(false);
            } else {
                $path = $this->path->getExecutablePath($this->package, $arch);
                $compiler->saveOutputAs($path)->makeExecutable();
            }
        }
    }
}
