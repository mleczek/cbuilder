<?php


namespace Mleczek\CBuilder\Console\Commands;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Run extends Command
{
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
        // TODO: ...
    }
}