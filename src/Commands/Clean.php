<?php


namespace Mleczek\CBuilder\Commands;


use Mleczek\CBuilder\Configuration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

class Clean extends Command
{
    /**
     * @Inject
     * @var Filesystem
     */
    private $fs;

    /**
     * @Inject
     * @var Configuration
     */
    private $config;

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('clean')
            ->setDescription('Remove artifacts produced by the build command.');
            //->addOption('modules', 'm', InputOption::VALUE_NONE, 'Perform cleaning also for modules artifacts.'); TODO: Implement...
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
        try {
            $output->write('Clean... ');

            $dir = $this->config->get('compilers.outputDir');
            $this->fs->remove($dir);

            $output->write('<info>OK</info>');
            return 0; // means "ok"
        }
        catch(IOException $e) {
            $output->writeln('<fg=red>FAIL</>');
            $output->write("<error>{$e->getMessage()}</error>");
            return -1;
        }
    }

}