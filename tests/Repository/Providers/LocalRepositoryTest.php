<?php

namespace Mleczek\CBuilder\Tests\Repository\Providers;

use Mleczek\CBuilder\Environment\Filesystem;
use Mleczek\CBuilder\Package\Factory;
use Mleczek\CBuilder\Package\Package;
use Mleczek\CBuilder\Package\Remote;
use Mleczek\CBuilder\Repository\Exceptions\PackageNotFoundException;
use Mleczek\CBuilder\Repository\Providers\LocalRepository;
use Mleczek\CBuilder\Tests\TestCase;
use Mleczek\CBuilder\Version\Providers\ConstVersionResolver;

class LocalRepositoryTest extends TestCase
{
    public function testGetExisting()
    {
        $fs = new Filesystem();
        $fs->touchDir('temp/org/package');

        $package = $this->createMock(Package::class);
        $factory = $this->createMock(Factory::class);
        $factory->expects($this->once())
            ->method('makeFromDir')
            ->with('temp/org/package')
            ->willReturn($package);

        $repo = new LocalRepository($fs, $factory, 'temp');

        $remote = $this->createMock(Remote::class);
        $factory->expects($this->once())
            ->method('makeRemote')
            ->with($repo, $package)
            ->willReturn($remote);

        $this->assertEquals($remote, $repo->get('org/package'));
    }

    public function testGetWhenRootDirNotExists()
    {
        $fs = new Filesystem();
        $factory = $this->createMock(Factory::class);

        $this->expectException(PackageNotFoundException::class);

        $repo = new LocalRepository($fs, $factory, 'temp');
        $repo->get('org/package');
    }

    public function testGetNonExisting()
    {
        $fs = new Filesystem();
        $fs->touchDir('temp/org');

        $factory = $this->createMock(Factory::class);
        $repo = new LocalRepository($fs, $factory, 'temp');

        $this->expectException(PackageNotFoundException::class);
        $repo->get('org/package');
    }

    public function testGetDir()
    {
        $fs = new Filesystem();
        $factory = $this->createMock(Factory::class);

        $repo = new LocalRepository($fs, $factory, 'temp');
        $this->assertEquals('temp', $repo->getDir());
    }
}
