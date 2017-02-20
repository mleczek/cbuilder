<?php

namespace Mleczek\CBuilder\Tests\Environment;

use Mleczek\CBuilder\Environment\Exceptions\InvalidPathException;
use Mleczek\CBuilder\Environment\Exceptions\UnknownException;
use Mleczek\CBuilder\Environment\Filesystem;
use Mleczek\CBuilder\Tests\TestCase;

class FilesystemTest extends TestCase
{
    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * Called before each test is executed.
     */
    protected function setUp()
    {
        $this->fs = new Filesystem();
    }

    public function testWorkingDir()
    {
        $actual = $this->fs->workingDir();
        $expected = realpath(self::ROOT_DIR);

        $this->assertInternalType('string', $actual);
        $this->assertEquals($expected, $actual);
    }

    public function testWorkingDirAlias()
    {
        $fs = $this->createPartialMock(Filesystem::class, ['workingDir']);
        $fs->expects($this->once())->method('workingDir');

        $fs->cwd();
    }

    public function testPath()
    {
        $actual = $this->fs->path('lorem', '\\lipsum//dolor');
        $expected = 'lorem/lipsum/dolor';

        $this->assertInternalType('string', $actual);
        $this->assertEquals($expected, $actual);
    }

    public function testTouchDir()
    {
        $this->assertFileNotExists('temp');
        $this->fs->touchDir('temp/lorem');
        $this->assertDirectoryExists('temp/lorem');
    }

    public function testTouchExistingDir()
    {
        $this->fs->touchDir('temp/lorem');
        $this->fs->touchDir('temp/lorem');
        $this->assertDirectoryExists('temp/lorem');
    }

    public function testTouchFileAsDir()
    {
        $this->expectException(InvalidPathException::class);
        $this->fs->touchFile('temp/lorem');
        $this->fs->touchDir('temp/lorem');
    }

    public function testTouchFile()
    {
        $this->assertDirectoryNotExists('temp');
        $this->fs->touchFile('temp/lorem/lipsum.txt');
        $this->assertFileExists('temp/lorem/lipsum.txt');
    }

    public function testTouchExistingFile()
    {
        $this->fs->touchFile('temp/lorem/lipsum.txt');
        $this->fs->touchFile('temp/lorem/lipsum.txt');
        $this->assertFileExists('temp/lorem/lipsum.txt');
    }

    public function testTouchDirAsFile()
    {
        $this->expectException(InvalidPathException::class);
        $this->fs->touchDir('temp/lorem');
        $this->fs->touchFile('temp/lorem');
    }

    /**
     * TODO: Debug on Linux to detect the issues
     *
     * @requires OS WIN32|WINNT|Windows
     */
    public function testTouchInvalidFileName()
    {
        $this->expectException(UnknownException::class);

        // The '\0' is an illegal symbol for linux file name
        // and '*' is an illegal symbol for windows file name.
        $this->fs->touchFile('temp/!@#$%^&*()_\0+');
    }

    /**
     * TODO: Debug on Linux to detect the issues
     *
     * @requires OS WIN32|WINNT|Windows
     */
    public function testTouchInvalidDirName()
    {
        $this->expectException(UnknownException::class);

        // The '\0' is an illegal symbol for linux file name
        // and '*' is an illegal symbol for windows file name.
        $this->fs->touchDir('temp/!@#$%^&*()_\0+');
    }

    public function testIsFile()
    {
        $this->assertTrue($this->fs->isFile('composer.json'));
        $this->assertFalse($this->fs->isFile('tests'));
    }

    public function testIsFileAlias()
    {
        $fs = $this->createPartialMock(Filesystem::class, ['isFile']);
        $fs->expects($this->once())->method('isFile');

        $fs->existsFile('anything');
    }

    public function testIsDir()
    {
        $this->assertFalse($this->fs->isDir('composer.json'));
        $this->assertTrue($this->fs->isDir('tests'));
    }

    public function testIsDirAlias()
    {
        $fs = $this->createPartialMock(Filesystem::class, ['isDir']);
        $fs->expects($this->once())->method('isDir');

        $fs->existsDir('anything');
    }

    public function testRemoveFile()
    {
        $this->fs->touchFile('temp/lorem/lipsum.txt');
        $this->assertFileExists('temp/lorem/lipsum.txt');
        $this->fs->removeFile('temp/lorem/lipsum.txt');
        $this->assertFileNotExists('temp/lorem/lipsum.txt');
    }

    public function testRemoveDirAsFile()
    {
        $this->fs->touchDir('temp/lorem');
        $this->assertDirectoryExists('temp/lorem');

        $this->expectException(InvalidPathException::class);
        $this->fs->removeFile('temp/lorem');
    }

    public function testRemoveNonExistingFile()
    {
        $this->assertFileNotExists('temp/lorem/lipsum.txt');
        $this->fs->removeFile('temp/lorem/lipsum.txt');
    }

    public function testRemoveDir()
    {
        $this->fs->touchDir('temp/lorem/lipsum');
        $this->assertDirectoryExists('temp/lorem/lipsum');
        $this->fs->removeDir('temp/lorem/lipsum');
        $this->assertDirectoryNotExists('temp/lorem/lipsum');
        $this->assertDirectoryExists('temp/lorem');
    }

    public function testRemoveNonExistingDir()
    {
        $this->assertDirectoryNotExists('temp/lorem/lipsum');
        $this->fs->removeDir('temp/lorem/lipsum');
    }

    public function testRemoveFileAsDir()
    {
        $this->fs->touchFile('temp/lorem');
        $this->assertFileExists('temp/lorem');

        $this->expectException(InvalidPathException::class);
        $this->fs->removeDir('temp/lorem');
    }

    public function testRemovePath()
    {
        $this->fs->touchFile('temp/lorem');
        $this->assertFileExists('temp/lorem');
        $this->fs->removePath('temp/lorem');
        $this->assertFileNotExists('temp/lorem');

        $this->fs->touchDir('temp/lorem');
        $this->assertFileExists('temp/lorem');
        $this->fs->removePath('temp/lorem');
        $this->assertDirectoryNotExists('temp/lorem');
    }

    public function testWriteFile()
    {
        $path = 'temp/lorem';
        $content = 'lorem lipsum dolor...';

        $this->assertFileNotExists('temp/lorem');
        $this->fs->writeFile($path, $content);
        $this->assertEquals($content, file_get_contents($path));
    }

    public function testWriteExistingFile()
    {
        $path = 'temp/lorem';
        $content = 'lorem lipsum dolor...';

        $this->fs->writeFile($path, '...');
        $this->fs->writeFile($path, $content);
        $this->assertEquals($content, file_get_contents($path));
    }

    public function testReadFile()
    {
        $content = 'abc';
        $path = 'temp/lorem';

        $this->fs->writeFile($path, $content);
        $this->assertEquals($content, $this->fs->readFile($path));
    }

    public function testReadNonExistingFile()
    {
        $this->expectException(InvalidPathException::class);
        $this->fs->readFile('temp/lorem');
    }

    public function testListFiles()
    {
        $pattern = '[a-z]+\.(txt|md)';
        $files = [
            'temp/lorem/lipsum/dolor.txt' => true,
            'temp/lorem/dolor.txt' => true,
            'temp/lorem/lipsum/dolor.md' => true,
            'temp/lipsum.txt' => true,
            'temp/lipsum.md' => true,
            'temp/lorem/lipsum/.txt' => false,
            'temp/lorem/dol0r.txt' => false,
            'temp/lorem/lipsum/dolor.cpp' => false,
        ];

        // Create files and get array of paths matching pattern.
        $matches = [];
        foreach ($files as $file => $matchPattern) {
            if ($matchPattern) {
                $matches[] = $file;
            }

            $this->fs->touchFile($file);
        }

        // Assertions.
        $result = $this->fs->listFiles('temp', $pattern);

        $this->assertTrue(array_diff($result, $matches) == []);
        $this->assertTrue(array_diff($matches, $result) == []);
    }

    public function testListFilesNonExistingDir()
    {
        $this->expectException(InvalidPathException::class);
        $this->fs->listFiles('temp');
    }

    /**
     * TODO: Debug on Linux to detect the issues
     *
     * @requires OS WIN32|WINNT|Windows
     */
    public function testRemoveUsedFile()
    {
        $this->fs->touchFile('temp/file');

        $file = fopen('temp/file', 'r');
        flock($file, LOCK_SH);

        try {
            $this->fs->removeFile('temp/file');
            $this->fail();
        } catch (UnknownException $e) {
            // Exception should occur
        }

        $this->assertFileExists('temp/file');

        flock($file, LOCK_UN);
        fclose($file);
    }
}
