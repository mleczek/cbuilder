<?php

namespace Mleczek\CBuilder\Tests\Dependency;

use Mleczek\CBuilder\Dependency\Uninstaller;
use Mleczek\CBuilder\Environment\Config;
use Mleczek\CBuilder\Environment\Filesystem;
use Mleczek\CBuilder\Lock\Exceptions\NotFoundLockEntryException;
use Mleczek\CBuilder\Lock\Factory;
use Mleczek\CBuilder\Lock\Lock;
use Mleczek\CBuilder\Tests\TestCase;

class UninstallerTest extends TestCase
{
    /**
     * @var Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $fs;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $config;

    /**
     * @var Factory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $lockFactory;

    /**
     * @var Uninstaller
     */
    protected $uninstaller;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->fs = $this->createMock(Filesystem::class);
        $this->config = $this->createMock(Config::class);
        $this->lockFactory = $this->createMock(Factory::class);

        $this->uninstaller = new Uninstaller($this->fs, $this->config, $this->lockFactory);
    }

    public function testUninstallingNotExistingDependencyFails()
    {
        $lock = $this->createMock(Lock::class);
        $this->lockFactory->method('makeFromFileOrEmpty')->willReturn($lock);
        $lock->method('remove')->with('org/package')->willThrowException(new NotFoundLockEntryException());

        $this->expectException(NotFoundLockEntryException::class);
        $this->uninstaller->uninstall(['org/package']);
    }

    public function testUninstallerUpdatesInstalledLockFile()
    {
        $this->config->expects($this->exactly(3))->method('get')
            ->withConsecutive(['modules.output_dir'], ['modules.meta_dir'], ['modules.installed_lock'])
            ->willReturnOnConsecutiveCalls('cmodules', '.meta', 'installed.lock');

        $this->fs->expects($this->at(0))->method('path')
            ->with('cmodules', '.meta', 'installed.lock')
            ->willReturn('cmodules/.meta/installed.lock');

        $lock = $this->createMock(Lock::class);
        $lock->expects($this->once())->method('save')
            ->with('cmodules/.meta/installed.lock');

        $this->lockFactory->method('makeFromFileOrEmpty')
            ->with('cmodules/.meta/installed.lock')
            ->willReturn($lock);

        $this->uninstaller->uninstall(['org/package']);
    }

    public function testUninstallingExistingDependencyPass()
    {
        $lock = $this->createMock(Lock::class);
        $lock->expects($this->once())->method('remove')->with('org/package');
        $this->lockFactory->method('makeFromFileOrEmpty')->willReturn($lock);

        $this->config->method('get')->willReturn('cmodules');
        $this->fs->expects($this->at(1))->method('path')->with('cmodules', 'org/package')->willReturn('cmodules/org/package');
        $this->fs->expects($this->once())->method('removeDir')->with('cmodules/org/package');

        $this->uninstaller->uninstall(['org/package']);
    }

    public function testUninstallingAllDependencies()
    {
        $this->config->method('get')
            ->with('modules.output_dir')
            ->willReturn('cmodules');

        $this->fs->expects($this->once())
            ->method('removeDir')
            ->with('cmodules');

        $this->uninstaller->uninstallAll();
    }
}
