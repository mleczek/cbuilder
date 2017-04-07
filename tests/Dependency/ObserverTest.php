<?php

namespace Mleczek\CBuilder\Tests\Dependency;

use Mleczek\CBuilder\Dependency\Observer;
use Mleczek\CBuilder\Environment\Config;
use Mleczek\CBuilder\Environment\Filesystem;
use Mleczek\CBuilder\Lock\Factory;
use Mleczek\CBuilder\Lock\Lock;
use Mleczek\CBuilder\Tests\TestCase;

class ObserverTest extends TestCase
{
    /**
     * @var Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $fs;

    /**
     * @var Factory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $factory;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $config;

    /**
     * @var Observer
     */
    protected $observer;

    public function setUp()
    {
        $this->fs = $this->createMock(Filesystem::class);
        $this->factory = $this->createMock(Factory::class);

        $this->config = $this->createMock(Config::class);
        $this->config->method('get')->willReturnMap([
            ['modules.output_dir', 'cmodules'],
            ['modules.meta_dir', '.meta'],
        ]);

        $this->observer = new Observer($this->fs, $this->factory, $this->config);
    }

    public function testEmptyResults()
    {
        $dir = 'cmodules/.meta/installed.lock';

        $lock = $this->createMock(Lock::class);
        $lock->method('packages')->willReturn([]);

        $this->factory->expects($this->once())->method('makeFromFileOrEmpty')->with($dir)->willReturn($lock);
        $this->fs->expects($this->once())->method('listDirs')->with('cmodules', 2)->willReturn([]);

        $this->observer->observe();
        $this->assertEquals([], $this->observer->getAmbiguous());
        $this->assertEquals([], $this->observer->getInstalled());
    }

    public function testLockedButNotInstalled()
    {
        $lock = $this->createMock(Lock::class);
        $lock->method('packages')->willReturn(['org/package' => '1.2']);

        $this->factory->expects($this->once())->method('makeFromFileOrEmpty')->willReturn($lock);
        $this->fs->expects($this->once())->method('listDirs')->willReturn([]);

        $this->observer->observe();
        $this->assertEquals(['org/package'], $this->observer->getAmbiguous());
        $this->assertEquals([], $this->observer->getInstalled());
    }

    public function testInstalledButNotLocked()
    {
        $lock = $this->createMock(Lock::class);
        $lock->method('packages')->willReturn([]);

        $this->factory->expects($this->once())->method('makeFromFileOrEmpty')->willReturn($lock);
        $this->fs->expects($this->once())->method('listDirs')->willReturn(['cmodules/org/package']);

        $this->observer->observe();
        $this->assertEquals(['org/package'], $this->observer->getAmbiguous());
        $this->assertEquals([], $this->observer->getInstalled());
    }

    public function testInstalledAndLocked()
    {
        $lock = $this->createMock(Lock::class);
        $lock->method('packages')->willReturn([
            'org/package' => '1.3',
            'org/locked' => '2.8.1',
        ]);

        $this->factory->expects($this->once())->method('makeFromFileOrEmpty')->willReturn($lock);
        $this->fs->expects($this->once())->method('listDirs')->willReturn([
            'cmodules/org/package',
            'cmodules/org/installed',
        ]);

        $this->observer->observe();
        $this->assertEquals(['org/locked', 'org/installed'], $this->observer->getAmbiguous());
        $this->assertEquals(['org/package' => '1.3'], $this->observer->getInstalled());
    }
}
