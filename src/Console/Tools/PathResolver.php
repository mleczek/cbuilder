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
                'dynamic' => '.dll',
            ],
        ],
        'linux' => [
            'project' => '',
            'library' => [
                'static' => '.a',
                'dynamic' => '.so',
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
     * @return string
     */
    public function getOutputDir()
    {
        return $this->env->config('compilers.output_dir');
    }

    /**
     * @param Package $package
     * @return string
     */
    public function getOutputPath(Package $package, $arch)
    {
        $name = str_replace('/', '.', $package->getName());

        $os = $this->env->isWindows() ? 'windows' : 'linux';
        $ext = self::EXTENSIONS[$os][$package->getType()];
        if($package->getType() == 'library') {
            // TODO: ...
        }

        return $package->getDir() .'/'. $this->getOutputDir() .'/'. $arch .'/'. $name . $ext;
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