<?php

namespace Mleczek\CBuilder\Console\Commands;

use Mleczek\CBuilder\System\Filesystem;
use Symfony\Component\Console\Command\Command;
use Mleczek\CBuilder\Console\Tools\PathResolver;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Clean extends Command
{
    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var PathResolver
     */
    private $path;

    /**
     * @param Filesystem $fs
     * @param PathResolver $path
     */
    public function __construct(Filesystem $fs, PathResolver $path)
    {
        parent::__construct();

        $this->fs = $fs;
        $this->path = $path;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('clean')
            ->setDescription('Remove output of the build command.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->fs->remove($this->path->getOutputDir());
    }
}
