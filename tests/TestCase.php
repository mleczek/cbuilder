<?php

namespace Mleczek\CBuilder\Tests;

use Mleczek\CBuilder\Environment\Filesystem;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    const ROOT_DIR = __DIR__ . '/..';

    /**
     * Called before the first test of this test class is run.
     */
    public static function setUpBeforeClass()
    {
        self::assertFileNotExists(self::ROOT_DIR .'/temp');
        self::assertDirectoryNotExists(self::ROOT_DIR .'/temp');
    }

    /**
     * Called after each test is executed.
     */
    protected function tearDown()
    {
        $fs = new Filesystem();
        $fs->removePath(self::ROOT_DIR . '/temp');
    }
}
