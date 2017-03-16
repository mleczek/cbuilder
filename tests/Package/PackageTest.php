<?php

namespace Mleczek\CBuilder\Tests\Package;

use DI\Container;
use DI\ContainerBuilder;
use Mleczek\CBuilder\Constraint\Parser;
use Mleczek\CBuilder\Environment\Config;
use Mleczek\CBuilder\Package\Exceptions\InvalidTypeException;
use Mleczek\CBuilder\Package\Exceptions\UnrecognizedArchitectureException;
use Mleczek\CBuilder\Package\Exceptions\UnrecognizedLinkingException;
use Mleczek\CBuilder\Package\Exceptions\UnrecognizedPlatformException;
use Mleczek\CBuilder\Package\Exceptions\UnrecognizedTypeException;
use Mleczek\CBuilder\Package\Package;
use Mleczek\CBuilder\Repository\Exceptions\UnknownRepositoryTypeException;
use Mleczek\CBuilder\Repository\Repository;
use Mleczek\CBuilder\Tests\TestCase;

class PackageTest extends TestCase
{
    /**
     * @var Parser|\PHPUnit_Framework_MockObject_MockObject
     */
    private $parser;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $config;

    /**
     * Called before each test is executed.
     */
    protected function setUp()
    {
        $di = ContainerBuilder::buildDevContainer();
        $this->parser = new Parser($di);
        $this->config = $this->createMock(Config::class);
    }

    public function testGetJson()
    {
        $json = (object)[
            'x' => 3,
            'y' => true,
            'z' => 'lorem',
        ];

        $package = new Package($this->parser, $this->config, $json);
        $this->assertEquals($json, $package->getJson());
    }

    public function testGetIncludeDir()
    {
        $json = json_decode('{"include": "temp/dir"}');
        $package = new Package($this->parser, $this->config, $json);
        $this->assertEquals($json->include, $package->getIncludeDir());
    }

    public function testGetIncludeDirDefaultValue()
    {
        $json = json_decode('{}');
        $package = new Package($this->parser, $this->config, $json);
        $this->assertEquals('include', $package->getIncludeDir());
    }

    public function testGetSourceDir()
    {
        $json = json_decode('{"source": "temp/dir"}');
        $package = new Package($this->parser, $this->config, $json);
        $this->assertEquals($json->source, $package->getSourceDir());
    }

    public function testGetSourceDirDefaultValue()
    {
        $json = json_decode('{}');
        $package = new Package($this->parser, $this->config, $json);
        $this->assertEquals('src', $package->getSourceDir());
    }

    public function testGetCompilers()
    {
        $json = json_decode('{"compiler": {"gcc": "^5.3", "clang": "*"}}');
        $package = new Package($this->parser, $this->config, $json);
        $this->assertEquals($json->compiler, $package->getCompilers());
    }

    public function testGetCompilersDefaultValue()
    {
        $json = json_decode('{}');
        $package = new Package($this->parser, $this->config, $json);
        $this->assertEquals([], $package->getCompilers());
    }

    public function testGetPlatforms()
    {
        $json = json_decode('{"platform": "windows"}');
        $package = new Package($this->parser, $this->config, $json);
        $this->assertEquals([$json->platform], $package->getPlatforms());
    }

    public function testGetPlatformsArray()
    {
        $json = json_decode('{"platform": ["windows", "linux", "mac"]}');
        $package = new Package($this->parser, $this->config, $json);
        $this->assertEquals($json->platform, $package->getPlatforms());
    }

    public function testGetPlatformsDefaultValue()
    {
        $json = json_decode('{}');
        $package = new Package($this->parser, $this->config, $json);
        $this->assertEquals(Package::AVAILABLE_PLATFORMS, $package->getPlatforms());
    }

    public function testGetPlatformsUnsupportedValues()
    {
        $json = json_decode('{"platform": "lorem"}');
        $package = new Package($this->parser, $this->config, $json);

        $this->expectException(UnrecognizedPlatformException::class);
        $package->getPlatforms();
    }

    public function testGetArchitectures()
    {
        $json = json_decode('{"arch": "x86"}');
        $package = new Package($this->parser, $this->config, $json);
        $this->assertEquals([$json->arch], $package->getArchitectures());
    }

    public function testGetArchitecturesArray()
    {
        $json = json_decode('{"arch": ["x86", "x64"]}');
        $package = new Package($this->parser, $this->config, $json);
        $this->assertEquals($json->arch, $package->getArchitectures());
    }

    public function testGetArchitecturesDefaultValue()
    {
        $json = json_decode('{}');
        $package = new Package($this->parser, $this->config, $json);
        $this->assertEquals(Package::AVAILABLE_ARCHITECTURES, $package->getArchitectures());
    }

    public function testGetArchitecturesUnsupportedValues()
    {
        $json = json_decode('{"arch": "lorem"}');
        $package = new Package($this->parser, $this->config, $json);

        $this->expectException(UnrecognizedArchitectureException::class);
        $package->getArchitectures();
    }

    public function testGetLinkingType()
    {
        $json = json_decode('{"linking": "static"}');
        $package = new Package($this->parser, $this->config, $json);
        $this->assertEquals([$json->linking], $package->getLinkingType());
    }

    public function testGetLinkingTypeForProject()
    {
        $json = json_decode('{"type": "project"}');
        $package = new Package($this->parser, $this->config, $json);

        $this->expectException(InvalidTypeException::class);
        $package->getLinkingType();
    }

    public function testGetLinkingTypeDefaultValue()
    {
        $json = json_decode('{}');
        $package = new Package($this->parser, $this->config, $json);
        $this->assertEquals(Package::AVAILABLE_LINKING, $package->getLinkingType());
    }

    public function testGetLinkingTypeUnsupportedValues()
    {
        $json = json_decode('{"linking": "lorem"}');
        $package = new Package($this->parser, $this->config, $json);

        $this->expectException(UnrecognizedLinkingException::class);
        $package->getLinkingType();
    }

    public function testIsLibrary()
    {
        $json = json_decode('{"type": "library"}');
        $package = new Package($this->parser, $this->config, $json);

        $this->assertTrue($package->isLibrary());
    }

    public function testIsNotLibrary()
    {
        $json = json_decode('{"type": "project"}');
        $package = new Package($this->parser, $this->config, $json);

        $this->assertFalse($package->isLibrary());
    }

    public function testGetName()
    {
        $json = json_decode('{"name": "org/package"}');
        $package = new Package($this->parser, $this->config, $json);
        $this->assertEquals($json->name, $package->getName());
    }

    public function testGetType()
    {
        $json = json_decode('{"type": "project"}');
        $package = new Package($this->parser, $this->config, $json);
        $this->assertEquals($json->type, $package->getType());
    }

    public function testGetTypeDefaultValue()
    {
        $json = json_decode('{}');
        $package = new Package($this->parser, $this->config, $json);
        $this->assertEquals('library', $package->getType());
    }

    public function testGetTypeUnsupportedValue()
    {
        $json = json_decode('{"type": "lorem"}');
        $package = new Package($this->parser, $this->config, $json);

        $this->expectException(UnrecognizedTypeException::class);
        $package->getType();
    }

    public function testGetDependencies()
    {
        $json = json_decode('{"dependencies": {"org/console": "^2.3:static", "org/hello": "*"}}');
        $expected = [
            (object)[
                'name' => 'org/console',
                'version' => '^2.3',
                'linking' => ['static'],
            ],
            (object)[
                'name' => 'org/hello',
                'version' => '*',
                'linking' => Package::AVAILABLE_LINKING,
            ],
        ];

        $package = new Package($this->parser, $this->config, $json);
        $this->assertEquals($expected, $package->getDependencies());
        $this->assertEquals([], $package->getDevDependencies());
    }

    public function testGetDependenciesDefaultValue()
    {
        $json = json_decode('{}');
        $package = new Package($this->parser, $this->config, $json);
        $this->assertEquals([], $package->getDependencies());
    }

    public function testGetDevDependencies()
    {
        $json = json_decode('{"dev-dependencies": {"org/console": "^2.3:static", "org/hello": "*"}}');
        $expected = [
            (object)[
                'name' => 'org/console',
                'version' => '^2.3',
                'linking' => ['static'],
            ],
            (object)[
                'name' => 'org/hello',
                'version' => '*',
                'linking' => Package::AVAILABLE_LINKING,
            ],
        ];

        $package = new Package($this->parser, $this->config, $json);
        $this->assertEquals($expected, $package->getDevDependencies());
        $this->assertEquals([], $package->getDependencies());
    }

    public function testGetDevDependenciesDefaultValue()
    {
        $json = json_decode('{}');
        $package = new Package($this->parser, $this->config, $json);
        $this->assertEquals([], $package->getDevDependencies());
    }

    public function testGetDebugDefines()
    {
        $json = json_decode('{"define": {"debug": {"DEBUG": true, "_DEBUG": true}}}');
        $expected = (object)[
            'DEBUG' => true,
            '_DEBUG' => true,
        ];

        $package = new Package($this->parser, $this->config, $json);
        $this->assertEquals($expected, $package->getDefines('debug'));
    }

    public function testGetReleaseDefines()
    {
        $json = json_decode('{"define": {"release": {"NDEBUG": true}}}');
        $expected = (object)['NDEBUG' => true];

        $package = new Package($this->parser, $this->config, $json);
        $this->assertEquals($expected, $package->getDefines('release'));
    }

    public function testGetDefinesDefaultValue()
    {
        $json = json_decode('{"define": {"release": {"NDEBUG": true}}}');
        $package = new Package($this->parser, $this->config, $json);

        // Empty result because we get debug (not release) macros.
        $this->assertEquals((object)[], $package->getDefines('debug'));
    }

    public function testGetSystemScripts()
    {
        $json = json_decode('{"scripts": {"before-build": ["cmd1", "cmd2"], "after-build:windows": "rm -r cache"}}');
        $expected = [
            'before-build' => ['cmd1', 'cmd2'],
            'after-build' => ['rm -r cache'],
        ];

        $package = new Package($this->parser, $this->config, $json);
        $this->assertEquals($expected, $package->getSystemScripts());
    }

    public function testGetSystemScriptsDefaultValue()
    {
        $json = json_decode('{}');
        $package = new Package($this->parser, $this->config, $json);
        $this->assertEquals([], $package->getSystemScripts());
    }

    public function testGetScripts()
    {
        $json = json_decode('{"scripts": {"before-build": "...", "custom:x86": "..."}}');
        $expected = [
            'before-build' => ['...'],
            'custom' => ['...'],
        ];

        $package = new Package($this->parser, $this->config, $json);
        $this->assertEquals($expected, $package->getScripts());
    }

    public function testGetScriptsArchFilter()
    {
        $json = json_decode('{"scripts": {"s1": "...", "s2:x86": "...", "s3:x64" : "..."}}');
        $expected = [
            's1' => ['...'],
            's2' => ['...'],
        ];

        $package = new Package($this->parser, $this->config, $json);
        $this->assertEquals($expected, $package->getScripts(['arch' => 'x86']));
    }

    public function testGetScriptsPlatformFilter()
    {
        $json = json_decode('{"scripts": {"s1": "...", "s2:windows": "...", "s3:linux" : "..."}}');
        $expected = [
            's1' => ['...'],
            's2' => ['...'],
        ];

        $package = new Package($this->parser, $this->config, $json);
        $this->assertEquals($expected, $package->getScripts(['platform' => 'windows']));
    }

    public function testGetScriptsLibraryTypeFilter()
    {
        $json = json_decode('{"scripts": {"s1": "...", "s2:static": "...", "s3:dynamic" : "..."}}');
        $expected = [
            's1' => ['...'],
            's2' => ['...'],
        ];

        $package = new Package($this->parser, $this->config, $json);
        $this->assertEquals($expected, $package->getScripts(['library' => 'static']));
    }

    public function testGetScriptsAllFilters()
    {
        $json = json_decode('{"scripts": {"s1:x86": "...", "s2:x86,windows,static": "...", "s3:x64" : "..."}}');
        $expected = [
            's1' => ['...'],
            's2' => ['...'],
        ];

        $package = new Package($this->parser, $this->config, $json);
        $this->assertEquals($expected, $package->getScripts([
            'arch' => 'x86',
            'platform' => 'windows',
            'library' => 'static',
        ]));
    }

    public function testGetScriptsDefaultValue()
    {
        $json = json_decode('{}');
        $package = new Package($this->parser, $this->config, $json);
        $this->assertEquals([], $package->getScripts());
    }

    public function testGetRepositories()
    {
        $this->config->method('get')->willReturn([]);

        $json = json_decode('{"repositories": [{"type": "local", "src": "temp/dir"}]}');
        $package = new Package($this->parser, $this->config, $json);
        $this->assertEquals($json->repositories, $package->getRepositories());
    }

    public function testGetRepositoriesValidOrder()
    {
        $defaultRepo = (object)[
            "type" => "official",
            "src" => "https://repository.cbuilder.pl/",
        ];

        $this->config->method('get')
            ->with('repositories.defaults')
            ->willReturn([$defaultRepo]);

        $json = json_decode('{"repositories": [{"type": "lorem", "src": "temp/dir"}, {"type": "lorem", "src": "temp/dir2"}]}');
        $package = new Package($this->parser, $this->config, $json);
        $this->assertEquals($json->repositories[0], $package->getRepositories()[0]);
        $this->assertEquals($json->repositories[1], $package->getRepositories()[1]);
        $this->assertEquals($defaultRepo, $package->getRepositories()[2]);
    }
}
