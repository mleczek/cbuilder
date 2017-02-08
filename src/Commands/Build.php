<?php


namespace Mleczek\CBuilder\Commands;


use Mleczek\CBuilder\Compilers\Providers\GCC;
use Mleczek\CBuilder\Compilers\Factory;
use Mleczek\CBuilder\Compilers\CompilersContainer;
use Mleczek\CBuilder\Compilers\ArtifactsBuilder;
use Mleczek\CBuilder\Configuration;
use Mleczek\CBuilder\Package;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Build extends Command
{
    /**
     * @Inject
     * @var Configuration
     */
    private $config;

    /**
     * @Inject
     * @var Factory
     */
    private $factory;

    /**
     * @Inject
     * @var CompilersContainer
     */
    protected $compilers;

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('build')
            ->setDescription('Run compilation and linking process and save produced artifacts.')
            ->addOption('debug', 'd', InputOption::VALUE_NONE, 'Use debug macros and include debug symbols as well as temp files.')
            ->addOption('compiler', null, InputOption::VALUE_REQUIRED, 'Strict use of a specific compiler.')
            ->addOption('arch', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Create artifacts only for a specific architecture(s).');
    }

    /**
     * Load all drivers registered in config file
     * (in `config/compilers.php`, `drivers` section)
     *
     * @param OutputInterface $output
     */
    private function registerAllCompilers(OutputInterface $output)
    {
        $drivers = $this->config->get('compilers.drivers');
        foreach ($drivers as $name => $driver) {
            $output->write("Register compiler ($name)... ");
            $supported = $this->compilers->register($name, $driver);

            if($supported) {
                $output->writeln('<info>OK</info>');
            } else {
                $output->writeln('<fg=red>FAIL</>');
                $output->writeln("<comment>The $name compiler wasn't found on your system.</comment>");
            }
        }
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
        $this->registerAllCompilers($output);

        $this->factory
            ->makeBuilderFor(Package::current())
            ->setDebugMode($input->getOption('debug'))
            ->setCompiler($input->getOption('compiler'))
            ->setArchitecture($input->getOption('arch'))
            ->run($output);

        return 0; // means "ok"
    }
}