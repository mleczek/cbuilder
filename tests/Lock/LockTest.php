<?php

namespace Mleczek\CBuilder\Tests\Lock;

use Mleczek\CBuilder\Environment\Filesystem;
use Mleczek\CBuilder\Lock\Exceptions\DuplicateLockEntryException;
use Mleczek\CBuilder\Lock\Exceptions\NotFoundLockEntryException;
use Mleczek\CBuilder\Lock\Lock;
use Mleczek\CBuilder\Tests\TestCase;

class LockTest extends TestCase
{
    public function testConstructorWithJson()
    {
        $fs = $this->createMock(Filesystem::class);
        $json = json_decode('{"dependencies": {"org/a": "3.1.8", "org/b": "1.0.2"}}');

        $lock = new Lock($fs, $json);
        $this->assertEquals(
            ['org/a' => '3.1.8', 'org/b' => '1.0.2'],
            $lock->packages()
        );
    }

    public function testConstructorWithoutJson()
    {
        $fs = $this->createMock(Filesystem::class);

        $lock = new Lock($fs);
        $this->assertEquals([], $lock->packages());
    }

    public function testAdd()
    {
        $fs = $this->createMock(Filesystem::class);
        $json = json_decode('{"dependencies": {"org/a": "3.1.8", "org/b": "1.0.2"}}');

        $lock = new Lock($fs, $json);
        $lock->add('org/c', '3.3');
        $this->assertEquals(
            ['org/a' => '3.1.8', 'org/b' => '1.0.2', 'org/c' => '3.3'],
            $lock->packages()
        );
    }

    public function testAddExisting()
    {
        $fs = $this->createMock(Filesystem::class);
        $json = json_decode('{"dependencies": {"org/a": "3.1.8"}}');

        $this->expectException(DuplicateLockEntryException::class);
        $lock = new Lock($fs, $json);
        $lock->add('org/a', '0.3.3');
    }

    public function testRemove()
    {
        $fs = $this->createMock(Filesystem::class);
        $json = json_decode('{"dependencies": {"org/a": "3.1.8", "org/b": "1.0.2"}}');

        $lock = new Lock($fs, $json);
        $lock->remove('org/a');
        $this->assertEquals(['org/b' => '1.0.2'], $lock->packages());
    }

    public function testRemoveNotExisting()
    {
        $fs = $this->createMock(Filesystem::class);
        $json = json_decode('{"dependencies": {"org/a": "3.1.8"}}');

        $this->expectException(NotFoundLockEntryException::class);
        $lock = new Lock($fs, $json);
        $lock->remove('org/b');
    }

    public function testSave()
    {
        $fs = $this->createMock(Filesystem::class);
        $json = json_decode('{"dependencies": {"org/a": "3.1.8"}}');

        $lock = new Lock($fs, $json);
        $lock->add('org/b', '3.3');
        $lock->remove('org/a');

        $file = 'temp/file';
        $expectedJson = json_encode([
            'dependencies' => ['org/b' => '3.3'],
        ]);

        $fs->expects($this->once())
            ->method('writeFile')
            ->with($file, $expectedJson);

        $lock->save($file);
    }
}
