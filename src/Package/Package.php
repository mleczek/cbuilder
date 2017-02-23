<?php

namespace Mleczek\CBuilder\Package;

use Mleczek\CBuilder\Constraint\Parser;
use Mleczek\CBuilder\Environment\Config;
use Mleczek\CBuilder\Package\Exceptions\InvalidTypeException;
use Mleczek\CBuilder\Package\Exceptions\UnrecognizedArchitectureException;
use Mleczek\CBuilder\Package\Exceptions\UnrecognizedLinkingException;
use Mleczek\CBuilder\Package\Exceptions\UnrecognizedPlatformException;
use Mleczek\CBuilder\Package\Exceptions\UnrecognizedTypeException;

/**
 * Represents immutable cbuilder.json file.
 */
class Package
{
    /**
     * CBuilder workflow callbacks names.
     *
     * @var string[]
     */
    const SYSTEM_SCRIPTS = [
        'before-build',
        'after-build',
    ];

    const AVAILABLE_TYPES = [
        'project', 'library',
    ];

    const AVAILABLE_PLATFORMS = [
        'windows', 'linux', 'mac',
    ];

    const AVAILABLE_ARCHITECTURES = [
        'x86', 'x64',
    ];

    const AVAILABLE_LINKING = [
        'static', 'dynamic',
    ];

    /**
     * @var object
     */
    private $json;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var Config
     */
    private $config;

    /**
     * Get package data from the json.
     * Before package can be safely used it must be validated.
     *
     * @see Validator
     * @param Parser $parser
     * @param Config $config
     * @param object $json Result of the json_decode function.
     */
    public function __construct(Parser $parser, Config $config, $json)
    {
        $this->parser = $parser;
        $this->json = $json;
        $this->config = $config;
    }

    /**
     * Get raw json passed to the constructor.
     *
     * @return object
     */
    public function getJson()
    {
        return $this->json;
    }

    /**
     * @return string Default is include.
     */
    public function getIncludeDir()
    {
        return $this->get('include', 'include');
    }

    /**
     * Get json value or default if not exists.
     *
     * @param string $key Dot notation.
     * @param mixed $default
     * @return mixed
     */
    protected function get($key, $default = null)
    {
        $result = $this->json;
        foreach (explode('.', $key) as $item) {
            if (!isset($result->{$item})) {
                return $default;
            }

            $result = $result->{$item};
        }

        return $result;
    }

    /**
     * @return string Default is src.
     */
    public function getSourceDir()
    {
        return $this->get('source', 'src');
    }

    /**
     * Get supported compilers.
     *
     * @return object Compiler name (key) with version constraint (value).
     */
    public function getCompilers()
    {
        return $this->get('compiler', []);
    }

    /**
     * Get supported platforms.
     *
     * @return string[]
     * @throws UnrecognizedPlatformException
     */
    public function getPlatforms()
    {
        $platforms = (array)$this->get('platform', self::AVAILABLE_PLATFORMS);

        // Check if all provided values are supported.
        $unrecognized = array_diff($platforms, self::AVAILABLE_PLATFORMS);
        if (!empty($unrecognized)) {
            $unrecognizedStr = implode("', '", $unrecognized);
            throw new UnrecognizedPlatformException("The '$unrecognizedStr' platforms are not supported.");
        }

        return $platforms;
    }

    /**
     * Get supported architectures.
     *
     * @return \string[]
     * @throws UnrecognizedArchitectureException
     */
    public function getArchitectures()
    {
        $architectures = (array)$this->get('arch', self::AVAILABLE_ARCHITECTURES);

        // Check if all provided values are supported.
        $unrecognized = array_diff($architectures, self::AVAILABLE_ARCHITECTURES);
        if (!empty($unrecognized)) {
            $unrecognizedStr = implode("', '", $unrecognized);
            throw new UnrecognizedArchitectureException("The '$unrecognizedStr' architectures are not supported.");
        }

        return $architectures;
    }

    /**
     * Get supported linking type.
     * Only for packages with library type.
     *
     * @return string[] Either static or dynamic (or both).
     * @throws InvalidTypeException
     * @throws UnrecognizedLinkingException
     */
    public function getLinkingType()
    {
        // Linking type is only available for library package type.
        if (!$this->isLibrary()) {
            $name = $this->getName();
            $type = $this->getType();
            throw new InvalidTypeException("Linking type can be retrieved only for library packages. The '$name' package type is '$type'.");
        }

        // Get json values or default ones.
        $linking = (array)$this->get('linking', ['static', 'dynamic']);

        // Check if all provided values are supported.
        $unrecognized = array_diff($linking, self::AVAILABLE_LINKING);
        if (!empty($unrecognized)) {
            $unrecognizedStr = implode("', '", $unrecognized);
            throw new UnrecognizedLinkingException("The '$unrecognizedStr' linking types are not supported.");
        }

        return $linking;
    }

    /**
     * Check whether package is a library.
     *
     * @return bool
     */
    public function isLibrary()
    {
        return $this->getType() === 'library';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->get('name');
    }

    /**
     * @return string Default is library.
     * @throws UnrecognizedTypeException
     */
    public function getType()
    {
        $type = $this->get('type', 'library');

        // Check if provided value is supported.
        if (!in_array($type, self::AVAILABLE_TYPES)) {
            $supported = implode(', ', self::AVAILABLE_TYPES);
            throw new UnrecognizedTypeException("The '$type' package type is not supported. Available types: $supported.");
        }

        return $type;
    }

    /**
     * @return object[] Each object contains name, version and linking keys.
     */
    public function getDependencies()
    {
        return $this->parseDependencies('dependencies');
    }

    /**
     * @param string $section
     * @return object[] Each object contains name, version and linking keys.
     */
    private function parseDependencies($section)
    {
        $dependencies = $this->get($section, []);

        $results = [];
        foreach ($dependencies as $name => $constraints) {
            $keyValues = $this->parser->parse($constraints);

            $results[] = (object)[
                'name' => $name,
                'version' => $keyValues->getKey(),
                'linking' => $keyValues->getValues() ?: self::AVAILABLE_LINKING,
            ];
        }

        return $results;
    }

    /**
     * @return array Package name (key) with version and linking constraint (value).
     */
    public function getDevDependencies()
    {
        return $this->parseDependencies('dev-dependencies');
    }

    /**
     * Get macros definitions.
     *
     * @param string $mode
     * @return object[]
     */
    public function getDefines($mode)
    {
        return $this->get("define.$mode", (object)[]);
    }

    /**
     * Get system scripts names and commands
     * (listed in SYSTEM_SCRIPTS constant).
     *
     * Example filters defined in the cbuilder.json:
     * "after-build:linux,x86": "rm -r cache",
     *
     * If script not contains eq. platform filter (windows/linux/mac...) then it matches all platforms,
     * it also applies to the options argument if platform key not exists then all platforms will match.
     *
     * @param array $options Limit results via arch, platform and linking filters.
     *                       Example: ['arch' => 'x86', 'library' => 'static']
     * @return array Script name (key) with array of bash commands (value).
     */
    public function getSystemScripts(array $options = [])
    {
        $scripts = $this->parseScripts($options);

        $results = [];
        foreach ($scripts as $name => $command) {
            if (in_array($name, self::SYSTEM_SCRIPTS)) {
                $results[$name] = $command;
            }
        }

        return $results;
    }

    /**
     * @see getScripts
     * @see getSystemScripts
     * @param array $options Limit results via arch, platform and linking filters.
     *                       Example: ['arch' => 'x86', 'library' => 'static']
     * @return array Script name (key) with array of bash commands (value).
     */
    private function parseScripts(array $options)
    {
        $scripts = $this->get('scripts', []);

        $results = [];
        foreach ($scripts as $name => $commands) {
            $keyValues = $this->parser->parse($name);

            // Filter architecture.
            if ($keyValues->hasAnyValue(self::AVAILABLE_ARCHITECTURES)) {
                if (isset($options['arch']) && !$keyValues->hasValue($options['arch'])) {
                    continue;
                }
            }

            // Filter platform.
            if ($keyValues->hasAnyValue(self::AVAILABLE_PLATFORMS)) {
                if (isset($options['platform']) && !$keyValues->hasValue($options['platform'])) {
                    continue;
                }
            }

            // Filter linking type.
            if ($keyValues->hasAnyValue(self::AVAILABLE_LINKING)) {
                if (isset($options['library']) && !$keyValues->hasValue($options['library'])) {
                    continue;
                }
            }

            $results[$keyValues->getKey()] = (array)$commands;
        }

        return $results;
    }

    /**
     * Get all scripts names and commands.
     *
     * Example filters defined in the cbuilder.json:
     * "custom-name:gcc": "rm -r cache",
     *
     * If script not contains eq. platform filter (windows/linux/mac...) then it matches all platforms,
     * it also applies to the options argument if platform key not exists then all platforms will match.
     *
     * @param array $options Limit results via arch, platform and linking filters.
     *                       Example: ['arch' => 'x86', 'library' => 'static']
     * @return array Script name (key) with array of bash commands (value).
     */
    public function getScripts(array $options = [])
    {
        return $this->parseScripts($options);
    }

    /**
     * Get repositories in order in which they are defined in json.
     *
     * @return object[] Each object contains type and src keys.
     */
    public function getRepositories()
    {
        $repositories = $this->get('repositories', []);

        // Register defaults repositories.
        $defaultRepositories = $this->config->get('repositories.defaults');
        $repositories = array_merge($repositories, $defaultRepositories);

        return $repositories;
    }
}
