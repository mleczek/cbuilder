<?php

namespace Mleczek\CBuilder\Tests\Repository\Providers;

use Mleczek\CBuilder\Downloader\Downloader;
use Mleczek\CBuilder\Downloader\Providers\LocalDownloader;
use Mleczek\CBuilder\Environment\Filesystem;
use Mleczek\CBuilder\Package\Factory as PackageFactory;
use Mleczek\CBuilder\Version\Factory as VersionFinderFactory;
use Mleczek\CBuilder\Downloader\Factory as DownloaderFactory;
use Mleczek\CBuilder\Package\Package;
use Mleczek\CBuilder\Package\Remote;
use Mleczek\CBuilder\Repository\Exceptions\PackageNotFoundException;
use Mleczek\CBuilder\Repository\Providers\LocalRepository;
use Mleczek\CBuilder\Tests\TestCase;
use Mleczek\CBuilder\Version\Providers\ConstVersionFinder;

class LocalRepositoryTest extends TestCase
{
    /**
     * @var PackageFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $packageFactory;

    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var VersionFinderFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $versionFinderFactory;

    /**
     * @var DownloaderFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $downloaderFactory;

    /**
     * @var LocalRepository
     */
    private $repository;

    public function setUp()
    {
        $this->fs = new Filesystem();
        $this->packageFactory = $this->createMock(PackageFactory::class);
        $this->versionFinderFactory = $this->createMock(VersionFinderFactory::class);
        $this->downloaderFactory = $this->createMock(DownloaderFactory::class);

        $this->repository = new LocalRepository(
            $this->fs,
            $this->packageFactory,
            $this->versionFinderFactory,
            $this->downloaderFactory,
            'temp'
        );
    }

    public function testGetExisting()
    {
        $this->fs->touchDir('temp/org/package');

        $package = $this->createMock(Package::class);
        $this->packageFactory->expects($this->once())
            ->method('makeFromDir')
            ->with('temp/org/package')
            ->willReturn($package);

        $versionFinder = $this->createMock(ConstVersionFinder::class);
        $this->versionFinderFactory->expects($this->once())
            ->method('makeConst')
            ->withAnyParameters()
            ->willReturn($versionFinder);

        $downloader = $this->createMock(LocalDownloader::class);
        $this->downloaderFactory->expects($this->once())
            ->method('makeLocal')
            ->with('temp/org/package')
            ->willReturn($downloader);

        $remote = $this->createMock(Remote::class);
        $this->packageFactory->expects($this->once())
            ->method('makeRemote')
            ->with($this->repository, $versionFinder, $downloader, $package)
            ->willReturn($remote);

        $this->assertEquals($remote, $this->repository->get('org/package'));
    }

    public function testGetWhenRootDirNotExists()
    {
        $this->expectException(PackageNotFoundException::class);
        $this->repository->get('org/package');
    }

    public function testGetNonExisting()
    {
        $this->fs->touchDir('temp/org');

        $this->expectException(PackageNotFoundException::class);
        $this->repository->get('org/package');
    }

    public function testGetDir()
    {
        $this->assertEquals('temp', $this->repository->getDir());
    }
}
