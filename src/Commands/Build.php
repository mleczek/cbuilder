<?php


namespace Mleczek\CBuilder\Commands;


use Mleczek\CBuilder\Compilers\Drivers\GCC;
use Mleczek\CBuilder\Compilers\Manager;
use Mleczek\CBuilder\Configuration\Store;
use Mleczek\CBuilder\Package;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Build extends Command
{
    /**
     * @var Store
     */
    private $config;

    /**
     * @var Package
     */
    private $package;

    /**
     * @var Manager
     */
    protected $compilers;

    /**
     * Build constructor.
     *
     * @param Store $config
     * @param Package $package
     * @param Manager $compilers
     */
    public function __construct(Store $config, Package $package, Manager $compilers)
    {
        parent::__construct();

        $this->config = $config;
        $this->package = $package;
        $this->compilers = $compilers;
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('build')
            ->setDescription('Build library/project artifacts.');
        //->setHelp('...') TODO: Add help description
        //->addOption('debug', 'd', InputOption::VALUE_NONE, 'Build using debug configuration.') // TODO: Implement
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
        // Register compilers
        $this->registerAllCompilers($output);

        // TODO: Get compiler using package preferences (many compilers in many versions)
        $compiler = $this->compilers->getOne();

        // TODO: Build all modules...

        // TODO: Get all source files...
        $sources = $this->package->getSourceFiles();

        // TODO: Get package type (library/project)

        // TODO: Link other modules

        // TODO: Set defines

        // TODO: Add include dir and all modules include dirs

        // TODO: Build library static and/or dynamic (depend on package preferences) or executable
        $arch = $this->package->getArchitectures();
        foreach ($arch as $archName) {
            $output->write("Building ($archName)... ");

            // TODO: Create output directories...
            // ...

            $stdout = [];
            $exitCode = $compiler->setArchitecture($archName)->compile($sources, $stdout);

            if ($exitCode == 0) {
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

        $output->write('Building finished.');
        return 0; // means "ok"
    }

}