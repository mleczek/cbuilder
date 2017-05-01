<?php

namespace Mleczek\CBuilder\Tests\Console\Commands;

use Mleczek\CBuilder\Console\Commands\CleanCommand;
use Mleczek\CBuilder\Environment\Config;
use Mleczek\CBuilder\Environment\Filesystem;
use Mleczek\CBuilder\Tests\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanCommandTest extends TestCase
{
    public function testCommand()
    {
        $fs = new Filesystem();
        $fs->touchDir('temp/build');
        $this->assertDirectoryExists('temp/build');

        chdir('temp');
        exec('php ../bin/cbuilder.php clean');
        chdir(CBUILDER_DIR);
        $this->assertDirectoryNotExists('temp/build');
    }

    public function testName()
    {
        $config = $this->createMock(Config::class);
        $fs = $this->createMock(Filesystem::class);

        $cmd = new CleanCommand($config, $fs);
        $this->assertEquals('clean', $cmd->getName());
    }

    public function testBehavior()
    {
        $config = $this->createMock(Config::class);
        $fs = $this->createMock(Filesystem::class);

        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $config->expects($this->atLeastOnce())
            ->method('get')
            ->with('compilers.output')
            ->willReturn('temp');

        $fs->method('existsDir')
            ->with('temp')
            ->willReturn(true);

        $fs->expects($this->once())
            ->method('removeDir')
            ->with('temp');

        $cmd = new CleanCommand($config, $fs);
        $cmd->run($input, $output);
    }
}
