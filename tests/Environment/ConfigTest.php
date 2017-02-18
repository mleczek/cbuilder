<?php

namespace Mleczek\CBuilder\Tests\Environment;

use Mleczek\CBuilder\Environment\Config;
use Mleczek\CBuilder\Environment\Exceptions\ConfigNotExistsException;
use Mleczek\CBuilder\Environment\Exceptions\InvalidPathException;
use Mleczek\CBuilder\Environment\Filesystem;
use Mleczek\CBuilder\Tests\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @var array
     */
    private $sample = [
        'key' => 5.2,
        'nested' => [
            'key' => 'value',
            'deep' => [
                'key' => false,
            ],
        ],
    ];

    public function testDotNotation()
    {
        $fs = $this->createPartialMock(Filesystem::class, ['existsDir', 'existsFile', 'readFile']);
        $fs->method('existsDir')->willReturn(true);

        $fs->method('existsFile')
            ->with('temp/file')
            ->willReturn(true);

        $fs->method('readFile')
            ->with('temp/file')
            ->willReturn($this->sample);

        $config = new Config($fs);
        $config->setDir('temp');

        $this->assertTrue($config->has('file.nested.key'));
        $this->assertEquals(
            $this->sample['nested']['key'],
            $config->get('file.nested.key')
        );
    }

    public function testSetNonExistingDir()
    {
        $fs = $this->createPartialMock(Filesystem::class, ['existsDir']);
        $fs->method('existsDir')->willReturn(false);

        $this->expectException(InvalidPathException::class);

        $config = new Config($fs);
        $config->setDir('temp');
    }

    public function testHasWithNotExistingFile()
    {
        $fs = $this->createPartialMock(Filesystem::class, []);
        $config = new Config($fs);

        $this->assertFalse($config->has('file.key'));
    }

    public function testGetWithNotExistingFile()
    {
        $fs = $this->createPartialMock(Filesystem::class, ['existsFile']);
        $fs->method('existsFile')->willReturn(false);

        $this->assertFalse($fs->existsFile('file'));
        $this->expectException(InvalidPathException::class);

        $config = new Config($fs);
        $config->get('file.key');
    }

    public function testHasWithNonExistingKey()
    {
        $fs = $this->createPartialMock(Filesystem::class, ['existsFile', 'readFile']);
        $fs->method('existsFile')->willReturn(true);
        $fs->method('readFile')->willReturn($this->sample);

        $config = new Config($fs);
        $this->assertTrue($config->has('file.nested'));
        $this->assertFalse($config->has('file.nested.not-exists'));
    }

    public function testGetWithNonExistingKey()
    {
        $fs = $this->createPartialMock(Filesystem::class, ['existsFile', 'readFile']);
        $fs->method('existsFile')->willReturn(true);
        $fs->method('readFile')->willReturn($this->sample);

        $config = new Config($fs);
        $this->expectException(ConfigNotExistsException::class);
        $config->get('file.nested.not-exists');
    }
}
