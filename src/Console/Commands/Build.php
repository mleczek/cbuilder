<?php

namespace Mleczek\CBuilder\Console\Commands;

use Mleczek\CBuilder\Console\Tools\CompilersService;
use Mleczek\CBuilder\Console\Tools\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Mleczek\CBuilder\Modules\Factory as ModulesFactory;

class Build extends Command
{
    /**
     * @var Factory
     */
    private $tools;

    /**
     * @var ModulesFactory
     */
    private $packages;

    /**
     * @var CompilersService
     */
    private $compilers;

    /**
     * @param Factory $tools
     * @param ModulesFactory $packages
     * @param $compilers
     */
    public function __construct(Factory $tools, ModulesFactory $packages, CompilersService $compilers)
    {
        parent::__construct();

        $this->tools = $tools;
        $this->packages = $packages;
        $this->compilers = $compilers;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('build')
            ->setDescription('Build current package.')
            ->addOption('debug', 'd', InputOption::VALUE_NONE, 'Generate debug symbols and intermediate files.')
            ->addOption('compiler', null, InputOption::VALUE_REQUIRED, 'Build package using specified compiler.')
            ->addOption('arch', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Compile only for specified architectures.', []);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->compilers->registerCompilers();

        // TODO: Check if all dependencies are installed

        $package = $this->packages->make();
        $this->tools->makeArtifactsBuilder($package)
            ->useDebugMode($input->getOption('debug'))
            ->setArchitectures($input->getOption('arch'))
            ->setCompiler($input->getOption('compiler'))
            ->build();
    }
}
