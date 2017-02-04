<?php


namespace Mleczek\CBuilder\Compilers\Drivers;


use Mleczek\CBuilder\Compilers\Contracts\Driver;

abstract class BaseDriver implements Driver
{
    const BUILD_DIR = "build";

    /**
     * @var string
     */
    protected $arch;

    /**
     * @var array
     */
    protected $defines = [];

    /**
     * @return string
     */
    public function getArchitecture()
    {
        return $this->arch;
    }

    /**
     * @param string $arch
     * @return $this
     */
    public function setArchitecture($arch)
    {
        $this->arch = $arch;
        return $this;
    }

    /**
     * Get preprocessor macros.
     *
     * @return array
     */
    public function getDefines()
    {
        return $this->defines;
    }

    /**
     * Register preprocessor macro.
     *
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function setDefine($name, $value)
    {
        $this->defines[$name] = $value;
        return $this;
    }

    /**
     * @return string
     */
    protected function getBuildPath()
    {
        return self::BUILD_DIR .'/'. $this->getArchitecture() .'/output.exe';
    }

    /**
     * Execute command and return exit code.
     *
     * @param array $params
     * @param null|array $output Will be filled with every line of output from the command.
     * @return int Process exit code.
     */
    public function run(array $params = [], array &$output = null)
    {
        // Note: "2>&1" redirects stderr to stdout
        $command = $this->getPath() .' '. implode(' ', $params) .' 2>&1';
        $exitCode = -1;

        exec($command, $output, $exitCode);
        return $exitCode;
    }
}