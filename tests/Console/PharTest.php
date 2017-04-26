<?php

namespace Mleczek\CBuilder\Tests\Console;

use Mleczek\CBuilder\Environment\Filesystem;
use Mleczek\CBuilder\Tests\TestCase;

class PharTest extends TestCase
{
    /**
     * Called before the first test of this test class is run.
     */
    public static function setUpBeforeClass()
    {
        $fs = new Filesystem();

        $pharFile = $fs->path(CBUILDER_DIR, 'bin/cbuilder.phar');
        $fs->removeFile($pharFile);

        exec('cd "' . CBUILDER_DIR . '" && composer run-script build');
        self::assertFileExists($pharFile);
    }

    public function testVersion()
    {
        $fs = new Filesystem();
        $pharFile = $fs->path(CBUILDER_DIR, 'bin/cbuilder.phar');

        $output = [];
        $exitCode = -1;
        exec('"' . $pharFile . '" --version', $output, $exitCode);

        $this->assertTrue($exitCode == 0);
        $this->assertContains('CBuilder - Package Manager for C/C++', $output[0]);
        $this->assertContains('1.0.0', $output[0]);
    }
}
