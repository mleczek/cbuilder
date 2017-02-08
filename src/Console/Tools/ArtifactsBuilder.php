<?php


namespace Mleczek\CBuilder\Console\Tools;


use Mleczek\CBuilder\Compilers\Compiler;
use Mleczek\CBuilder\Compilers\Container;
use Mleczek\CBuilder\Modules\Package;
use Mleczek\CBuilder\System\Environment;
use Mleczek\CBuilder\System\Filesystem;

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
     * @Inject
     * @var Environment
     */
    private $env;

    /**
     * @Inject
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @Inject
     * @var Container
     */
    private $compilers;
    /**
     * @var Filesystem
     */

    /**
     * @param Environment $env
     * @param Filesystem $fs
     * @param Container $compilers
     * @param Package $package
     */
    public function __construct(Environment $env, Filesystem $fs, Container $compilers, Package $package)
    {
        $this->env = $env;
        $this->filesystem = $fs;
        $this->compilers = $compilers;
        $this->package = $package;
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
     * @param string|null $compiler
     * @return $this
     */
    public function setCompiler($compiler)
    {
        $this->compiler = $compiler;
        return $this;
    }

    /**
     * Get compiler set by setter
     * or best suitable from the package file.
     *
     * @return Compiler
     */
    private function getCompiler()
    {
        // TODO: ...
        return $this->compilers->getOne();
    }

    /**
     * @param string|string[] $arch
     * @return $this
     */
    public function setArchitectures($arch)
    {
        $this->architectures = (array)$arch;
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
        // TODO: ...
        return ['x86', 'x64'];
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
        foreach($this->package->getDefines($buildMode) as $name => $value) {
            $compiler->define($name, $value);
        }

        // Build artifacts for each architecture
        foreach($architectures as $arch) {
            // TODO: Move logic of getting package dirs to separate service
            // FIXME: Use extension depends on the platform and package type
            $filename = str_replace('/', '.', $this->package->getName());
            $path = $this->env->config('compilers.output_dir') .'/'. $arch .'/'. $filename .'.exe';

            // Compiler expects that the output dir exists
            $this->filesystem->makeDir(dirname($path));

            $compiler->setArchitecture($arch)
                ->setSourceFiles($this->package->getSourceFiles())
                ->withDebugSymbols($this->debugMode)
                ->withIntermediateFiles($this->debugMode)
                ->saveOutputAs($path)
                ->compile();
        }
    }
}