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
        $fs = new Filesystem();
        $fs->touchDir('temp');

        $content = '<?php return ' . var_export($this->sample, true) . ';';
        $fs->writeFile('temp/file.php', $content);

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
        $fs = new Filesystem();
        $fs->touchDir('temp');

        $config = new Config($fs);
        $config->setDir('temp');

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
        $fs = new Filesystem();
        $fs->touchDir('temp');

        $content = '<?php return ' . var_export($this->sample, true) . ';';
        $fs->writeFile('temp/file.php', $content);

        $config = new Config($fs);
        $config->setDir('temp');
        $this->assertTrue($config->has('file.nested'));
        $this->assertFalse($config->has('file.nested.not-exists'));
    }

    public function testGetWithNonExistingKey()
    {
        $fs = new Filesystem();
        $fs->touchDir('temp');

        $content = '<?php return ' . var_export($this->sample, true) . ';';
        $fs->writeFile('temp/file.php', $content);

        $config = new Config($fs);
        $config->setDir('temp');
        $this->expectException(ConfigNotExistsException::class);
        $config->get('file.nested.not-exists');
    }
}
