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
     * @var \Mleczek\CBuilder\Configuration
     */
    private $config;

    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var CompilersContainer
     */
    protected $compilers;

    /**
     * Build constructor.
     *
     * @param \Mleczek\CBuilder\Configuration $config
     * @param Factory $factory
     * @param CompilersContainer $compilers
     */
    public function __construct(Configuration $config, Factory $factory, CompilersContainer $compilers)
    {
        parent::__construct();

        $this->config = $config;
        $this->factory = $factory;
        $this->compilers = $compilers;
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('build')
            ->setDescription('Build library/project artifacts.')
        //->setHelp('...') TODO: Add help description
        ->addOption('debug', 'd', InputOption::VALUE_NONE, 'Use debug macros and include debug symbols as well as temp files.');
        //->addOption('compiler', 'c', InputOption::VALUE_REQUIRED, 'Strict use of a specific compiler.') // TODO: Implement
        //->addOption('arch', 'a', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Create artifacts only for a specific architecture(s).'); // TODO: Implement
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
            ->makeRunner(Package::current())
            ->setDebugMode($input->getOption('debug'))
            ->run($output);

        return 0; // means "ok"
    }

}