<?php


namespace Mleczek\CBuilder\Console\Tools;


use Mleczek\CBuilder\Modules\Package;
use Mleczek\CBuilder\System\Environment;

class PathResolver
{
    const EXTENSIONS = [
        'windows' => [
            'project' => '.exe',
            'library' => [
                'static' => '.lib',
                'shared' => '.dll',
            ],
        ],
        'linux' => [
            'project' => '',
            'library' => [
                'static' => '.a',
                'shared' => '.so',
            ],
        ]
    ];

    /**
     * @var Environment
     */
    private $env;

    /**
     * @param Environment $env
     */
    public function __construct(Environment $env)
    {
        $this->env = $env;
    }

    /**
     * @param string|null $arch
     * @return string
     */
    public function getOutputDir($arch = null)
    {
        $dir = $this->env->config('compilers.output');
        if(!is_null($arch)) {
            return $dir .'/'. $arch;
        }

        return $dir;
    }

    /**
     * @param Package $package
     * @return string
     */
    public function getExecutablePath(Package $package, $arch)
    {
        $name = str_replace('/', '.', $package->getName());

        $os = $this->env->isWindows() ? 'windows' : 'linux';
        $ext = self::EXTENSIONS[$os][$package->getType()];

        return $package->getDir() .'/'. $this->getOutputDir($arch) .'/'. $name . $ext;
    }

    /**
     * @param Package $package
     * @param string $arch
     * @param bool $static
     * @return string
     */
    public function getLibraryPath($package, $arch, $static)
    {
        $name = str_replace('/', '.', $package->getName());

        $os = $this->env->isWindows() ? 'windows' : 'linux';
        $libType = $static ? 'static' : 'shared';
        $ext = self::EXTENSIONS[$os][$package->getType()][$libType];

        return $package->getDir() .'/'. $this->getOutputDir($arch) .'/'. $name . $ext;
    }

    /**
     * @return string
     */
    public function getPackageFileName()
    {
        return Package::FILE_NAME;
    }

    /**
     * @param string $dir
     * @return string
     */
    public function getPackageFilePath($dir)
    {
        return $dir .'/'. $this->getPackageFileName();
    }

    /**
     * @return string
     */
    public function getModulesDir()
    {
        return $this->env->config('modules.dir');
    }

    /**
     * @param string $module
     * @return string
     */
    public function getModuleDir($module)
    {
        return $this->getModulesDir() .'/'. $module;
    }
}