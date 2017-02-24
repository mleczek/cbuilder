<?php

namespace Mleczek\CBuilder\Tests\Package;

use Mleczek\CBuilder\Downloader\Downloader;
use Mleczek\CBuilder\Package\Package;
use Mleczek\CBuilder\Package\Remote;
use Mleczek\CBuilder\Repository\Repository;
use Mleczek\CBuilder\Tests\TestCase;
use Mleczek\CBuilder\Version\Finder;

class RemoteTest extends TestCase
{
    public function testGetters()
    {
        $repository = $this->createMock(Repository::class);
        $versionFinder = $this->createMock(Finder::class);
        $downloader = $this->createMock(Downloader::class);
        $package = $this->createMock(Package::class);

        $remote = new Remote($repository, $versionFinder, $downloader, $package);
        $this->assertEquals($repository, $remote->getRepository());
        $this->assertEquals($versionFinder, $remote->getVersionFinder());
        $this->assertEquals($downloader, $remote->getDownloader());
        $this->assertEquals($package, $remote->getPackage());
    }
}
