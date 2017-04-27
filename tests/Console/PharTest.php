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
        if (ini_get('phar.readonly') == true) {
            self::markTestSkipped('Creating phar disabled, set "phar.readonly = Off" in php.ini.');
        }

        $fs = new Filesystem();

        $pharFile = $fs->path(CBUILDER_DIR, 'bin/cbuilder.phar');
        $fs->removeFile($pharFile);

        exec('php bin/phar-create.php');
        exec('chmod +x "' . $pharFile . '"');
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
