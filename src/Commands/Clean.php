<?php


namespace Mleczek\CBuilder\Commands;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

class Clean extends Command
{
    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * Clean constructor.
     *
     * @param Filesystem $fs
     */
    public function __construct(Filesystem $fs)
    {
        parent::__construct();

        $this->fs = $fs;
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('clean')
            ->setDescription('Remove build command artifacts.')
            ->setHelp('Remove all artifacts created during executing the build command.')
            ->addOption('modules', 'm', InputOption::VALUE_NONE, 'Perform cleaning also for modules artifacts.');
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
            $this->fs->remove('build'); // FIXME: Move "build" dir name to configuration

            $output->write('<info>OK</info>');
            return 0; // means "ok"
        }
        catch(IOException $e) {
            // TODO: Support verbose levels
            $output->writeln('<fg=red>FAIL</>');
            $output->write("<error>{$e->getMessage()}</error>");
            return -1;
        }
    }

}