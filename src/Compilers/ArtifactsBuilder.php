<?php

namespace Mleczek\CBuilder\Compilers;

use Mleczek\CBuilder\Package;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Handler compiling and linking process
 * for specified package (including modules).
 *
 * @Injectable(scope="prototype")
 */
class ArtifactsBuilder
{
    /**
     * @Inject
     * @var Package
     */
    private $package;

    /**
     * @Inject
     * @var CompilersContainer
     */
    private $compilers;

    /**
     * @var bool
     */
    private $debugMode;

    /**
     * @param bool $enabled
     * @return $this
     */
    public function setDebugMode($enabled)
    {
        $this->debugMode = $enabled;
        return $this;
    }

    /**
     * @return string
     */
    private function getDefaultCompiler()
    {
        $this->package->getCompilers();
    }

    /**
     * Force to use specified compiler or get the default one.
     *
     * @param string|null $compiler
     * @return $this
     */
    public function setCompiler($compiler = null)
    {
        if(is_null($compiler)) {
            $compiler = $this->getDefaultCompiler();
        }

        $this->compiler = $compiler;
        return $this;
    }

    /**
     * Force to use specified compilers or get the default ones.
     *
     * @param string|null $arch Comma separated.
     * @return $this
     */
    public function setArchitecture($arch = null)
    {
        // TODO: ...

        return $this;
    }

    public function run(OutputInterface $output)
    {
        // TODO: Get compiler using package preferences (many compilers in many versions)
        $compiler = $this->compilers->getOne();

        // Set debug mode
        if($this->debugMode) {
            $compiler->includeDebugSymbols()
                ->includeTempFiles();
        }

        // TODO: Build all modules...

        // TODO: Get all source files...
        $sources = $this->package->getSourceFiles();

        // TODO: Get package type (library/project)

        // TODO: Link other modules

        // TODO: Set defines
        $modeName = $this->debugMode ? 'debug' : 'release';
        $defines = $this->package->getDefines($modeName);
        foreach ($defines as $name => $value) {
            $compiler->setDefine(strtoupper($name), $value);
        }

        // TODO: Add include dir and all modules include dirs

        // TODO: Build library static and/or dynamic (depend on package preferences) or executable
        $arch = $this->package->getArchitectures();
        $total = count($arch);
        $done = 0;

        foreach ($arch as $archName) {
            $output->write("Build ($archName)... ");

            // TODO: Create output directories...
            // ...

            $stdout = [];
            $exitCode = $compiler->setArchitecture($archName)->compile($sources, $stdout);

            if ($exitCode == 0) {
                ++$done;
                $output->writeln('<info>OK</info>');
            } else {
                $output->writeln('<fg=red>FAIL</>');
                $output->writeln(array_merge(
                    ['<error>======== COMPILER ERROR ========'],
                    $stdout,
                    ['================================</error>']
                ));
            }
        }

        // Build stats
        if ($done == $total) {
            $output->write("Build completed: <info>$done/$total</info>");
        } else if ($done > 0) {
            $output->write("Build completed: <comment>$done/$total</comment>");
        } else {
            $output->write("Build completed: <fg=red>$done/$total</>");
        }
    }
}