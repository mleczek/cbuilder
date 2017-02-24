<?php

namespace Mleczek\CBuilder\Tests\Downloader\Providers;

use Mleczek\CBuilder\Downloader\Exceptions\SourceNotExistsException;
use Mleczek\CBuilder\Downloader\Providers\LocalDownloader;
use Mleczek\CBuilder\Environment\Filesystem;
use Mleczek\CBuilder\Tests\TestCase;

class LocalDownloaderTest extends TestCase
{
    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var LocalDownloader
     */
    private $downloader;

    public function setUp()
    {
        $this->fs = new Filesystem();
        $this->downloader = new LocalDownloader($this->fs, 'temp');
    }

    public function testCheckStatusBeforeDownloading()
    {
        $this->assertFalse($this->downloader->success());
    }

    public function testDownload()
    {
        $called = false;
        $progress = function ($perc) use (&$called) {
            $called = true;
            $this->assertEquals(100, $perc);
        };

        $this->fs->touchDir('temp');
        $this->assertEquals('temp', $this->downloader->download('1.0', $progress));
        $this->assertTrue($this->downloader->success());
        $this->assertTrue($called);
    }

    public function testDownloadWithoutProgress()
    {
        $this->fs->touchDir('temp');
        $this->assertEquals('temp', $this->downloader->download('1.0'));
        $this->assertTrue($this->downloader->success());
    }

    public function testDownloadNotExistingDir()
    {
        $this->expectException(SourceNotExistsException::class);
        $this->downloader->download('1.0', null);
    }

    public function testDummyTo()
    {
        $this->assertEquals($this->downloader, $this->downloader->to('xyz'));
        $this->assertEquals('temp', $this->downloader->from());
    }
}
