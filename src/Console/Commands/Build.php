<?php

namespace Mleczek\CBuilder\Console\Commands;

use Mleczek\CBuilder\System\Environment;
use Mleczek\CBuilder\Compilers\Container;
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
     * @var Environment
     */
    private $env;

    /**
     * @var Container
     */
    private $compilers;

    /**
     * @param Factory $tools
     * @param ModulesFactory $packages
     * @param Environment $env
     * @param $compilers
     */
    public function __construct(Factory $tools, ModulesFactory $packages, Environment $env, Container $compilers)
    {
        parent::__construct();

        $this->tools = $tools;
        $this->packages = $packages;
        $this->env = $env;
        $this->compilers = $compilers;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('build')
            ->setDescription('Build package including required dependencies.')
            ->addOption('debug', 'd', InputOption::VALUE_NONE, 'Generate debug symbols and intermediate files.')
            ->addOption('compiler', null, InputOption::VALUE_REQUIRED, 'Build package using specified compiler.')
            ->addOption('arch', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Compile only for specified architectures.', [])
            ->addOption('module', 'm', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Build only specified module and linked dependencies.');
    }

    /**
     * Register all compilers.
     */
    private function registerCompilers()
    {
        $providers = $this->env->config('compilers.providers');
        foreach ($providers as $name => $provider) {
            $this->compilers->register($name, $provider);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->registerCompilers();

        // TODO: Select package...
        $package = $this->packages->make();

        $this->tools->makeArtifactsBuilder($package)
            ->useDebugMode($input->getOption('debug'))
            ->setArchitectures($input->getOption('arch'))
            ->setCompiler($input->getOption('compiler'))
            ->build();
    }
}
