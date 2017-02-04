<?php

namespace Mleczek\CBuilder\Compilers;

use Mleczek\CBuilder\Package;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Handler compiling and linking process
 * for specified package (including modules).
 */
class Runner
{
    /**
     * @var Package
     */
    private $package;

    /**
     * @var Manager
     */
    private $compilers;

    /**
     * Runner constructor.
     *
     * @param Package $package
     * @param Manager $compilers
     */
    public function __construct(Package $package, Manager $compilers)
    {
        $this->package = $package;
        $this->compilers = $compilers;
    }

    public function run(OutputInterface $output)
    {
        // TODO: Get compiler using package preferences (many compilers in many versions)
        $compiler = $this->compilers->getOne();

        // TODO: Build all modules...

        // TODO: Get all source files...
        $sources = $this->package->getSourceFiles();

        // TODO: Get package type (library/project)

        // TODO: Link other modules

        // TODO: Set defines
        $defines = $this->package->getDefines();
        foreach ($defines as $name => $value) {
            $compiler->setDefine($name, $value);
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
            $output->write("Build... <info>$done/$total</info>");
        } else if ($done > 0) {
            $output->write("Build... <comment>$done/$total</comment>");
        } else {
            $output->write("Build... <fg=red>$done/$total</>");
        }
    }
}