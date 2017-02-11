<?php


namespace Mleczek\CBuilder\Console\Commands;


use Mleczek\CBuilder\Console\Tools\PathResolver;
use Mleczek\CBuilder\System\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('clean')
            ->setDescription('Remove output of the build command.')
            ->addOption('module', 'm', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Clean output of the specified module.')
            ->addOption('nested', null, InputOption::VALUE_NONE, 'Perform operation also for all package dependencies.');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // TODO: select package, nested cleaning...

        $this->fs->remove($this->path->getOutputDir());
    }
}