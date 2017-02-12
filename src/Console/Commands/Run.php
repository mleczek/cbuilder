<?php


namespace Mleczek\CBuilder\Console\Commands;


use Mleczek\CBuilder\Console\Tools\PathResolver;
use Mleczek\CBuilder\Modules\Package;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Run extends Command
{
    /**
     * @var PathResolver
     */
    private $path;

    /**
     * @var Package
     */
    private $package;

    /**
     * @param PathResolver $path
     * @param Package $package
     */
    public function __construct(PathResolver $path, Package $package)
    {
        parent::__construct();

        $this->path = $path;
        $this->package = $package;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('run')
            ->setDescription('Run script or project executable.')
            ->addArgument('script', InputArgument::OPTIONAL, 'Run specified script.');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if(!is_null($input->getArgument('script'))) {
            // TODO: ...
            return $output->write("Script '{$input->getArgument('script')}' not found. Define script in cbuilder.json in 'scripts' section.");
        }

        if($this->package->getType() !== 'project') {
            return $output->write("Cannot run library package. Propably you'd like to specify script name from 'scripts' section in cbuilder.json.");
        }

        $arch = array_values($this->package->getArchitectures())[0];
        $cmd = $this->path->getExecutablePath($this->package, $arch);
        system(str_replace('/', '\\', $cmd)); // FIXME: Windows vs Linux path
    }
}