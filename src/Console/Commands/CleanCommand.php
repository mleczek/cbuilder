<?php

namespace Mleczek\CBuilder\Console\Commands;

use Mleczek\CBuilder\Environment\Config;
use Mleczek\CBuilder\Environment\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanCommand extends Command
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * CleanCommand constructor.
     *
     * @param Config $config
     * @param Filesystem $fs
     */
    public function __construct(Config $config, Filesystem $fs)
    {
        $this->config = $config;
        $this->fs = $fs;

        // Passing null means that name must be set in configure().
        parent::__construct(null);
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $outputDir = $this->config->get('compilers.output');

        $this->setName('clean')
            ->setDescription('Remove the build artifacts.')
            ->setHelp("Remove the '$outputDir' directory which contains build artifacts.");
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface $input An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     * @return null|int null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write('Cleaning... ');
        $outputDir = $this->config->get('compilers.output');

        if ($this->fs->existsDir($outputDir)) {
            $this->fs->removeDir($outputDir);
            $output->writeln('<info>OK</info>');
        } else {
            $output->writeln('<comment>Skipped</comment>');
        }
    }
}
