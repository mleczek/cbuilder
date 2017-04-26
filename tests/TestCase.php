<?php

namespace Mleczek\CBuilder\Tests;

use Mleczek\CBuilder\Environment\Filesystem;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * Called before the first test of this test class is run.
     */
    public static function setUpBeforeClass()
    {
        self::assertFileNotExists('temp');
        self::assertDirectoryNotExists('temp');
    }

    /**
     * Called after each test is executed.
     */
    protected function tearDown()
    {
        $fs = new Filesystem();
        $fs->removePath('temp');
    }
}
