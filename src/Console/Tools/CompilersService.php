<?php

namespace Mleczek\CBuilder\Console\Tools;

use Mleczek\CBuilder\Compilers\Container;
use Mleczek\CBuilder\System\Environment;

class CompilersService
{
    /**
     * @var Environment
     */
    private $env;

    /**
     * @var Container
     */
    private $compilers;

    /**
     * @param Environment $env
     * @param Container $compilers
     */
    public function __construct(Environment $env, Container $compilers)
    {
        $this->env = $env;
        $this->compilers = $compilers;
    }

    /**
     * Register all compilers defined in the
     * "compilers" configuration file.
     */
    public function registerCompilers()
    {
        $providers = $this->env->config('compilers.providers');
        foreach ($providers as $name => $provider) {
            $this->compilers->register($name, $provider);
        }
    }
}
