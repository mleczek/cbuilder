<?php

namespace Mleczek\CBuilder\Tests\Repository\Providers;

use Mleczek\CBuilder\Environment\Filesystem;
use Mleczek\CBuilder\Package\Factory;
use Mleczek\CBuilder\Package\Package;
use Mleczek\CBuilder\Repository\Exceptions\PackageNotFoundException;
use Mleczek\CBuilder\Repository\Providers\LocalRepository;
use Mleczek\CBuilder\Tests\TestCase;

class LocalRepositoryTest extends TestCase
{
    public function testGetExisting()
    {
        $fs = new Filesystem();
        $fs->touchDir('temp/org/package');

        $package = $this->createMock(Package::class);
        $factory = $this->createMock(Factory::class);
        $factory->expects($this->once())
            ->method('fromDir')
            ->with('temp/org/package')
            ->willReturn($package);

        $repo = new LocalRepository($fs, $factory);
        $repo->setSource('temp');

        $this->assertEquals($package, $repo->get('org/package'));
    }

    public function testGetWhenRootDirNotExists()
    {
        $fs = new Filesystem();
        $factory = $this->createMock(Factory::class);

        $this->expectException(PackageNotFoundException::class);

        $repo = new LocalRepository($fs, $factory);
        $repo->get('org/package');
    }

    public function testGetNonExisting()
    {
        $fs = new Filesystem();
        $fs->touchDir('temp/org');

        $factory = $this->createMock(Factory::class);
        $repo = new LocalRepository($fs, $factory);

        $this->expectException(PackageNotFoundException::class);
        $repo->get('org/package');
    }

    public function testSetAndGetSource()
    {
        $fs = new Filesystem();
        $factory = $this->createMock(Factory::class);

        $repo = new LocalRepository($fs, $factory);
        $repo->setSource('example');
        $this->assertEquals('example', $repo->getSource('example'));
    }
}
