<?php


namespace Mleczek\CBuilder\Compilers;

use DI\Container as DIContainer;
use Mleczek\CBuilder\Compilers\Exceptions\CompilerNotFoundException;


class Container
{
    /**
     * @var DIContainer
     */
    private $container;

    /**
     * @var Compiler[string]
     */
    private $compilers;

    /**
     * Container constructor.
     * @param $container
     */
    public function __construct(DIContainer $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $name
     * @param string $namespace Namespace to the class implementing Compiler interface.
     * @return bool True if compiler is supported, false otherwise.
     */
    public function register($name, $namespace)
    {
        $compiler = $this->container->make($namespace);
        if($compiler->isSupported()) {
            $this->compilers[$name] = $compiler;
        }

        return $compiler->isSupported();
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->compilers[$name]);
    }

    /**
     * @param string $name
     * @return Compiler
     * @throws CompilerNotFoundException
     */
    public function get($name)
    {
        if(!$this->has($name)) {
            throw new CompilerNotFoundException("The '$name' compiler not exists.");
        }

        return $this->compilers[$name];
    }

    /**
     * @return Compiler
     * @throws CompilerNotFoundException
     */
    public function getOne()
    {
        if(empty($this->compilers)) {
            throw new CompilerNotFoundException("No compilers found.");
        }

        foreach($this->compilers as $name => $provider) {
            return $provider;
        }
    }

    /**
     * @param string|string[] $compilers
     * @return Compiler
     * @throws CompilerNotFoundException
     */
    public function getOneOf($compilers)
    {
        foreach($this->compilers as $compiler) {
            if(in_array($compiler, $compilers)) {
                return $this->compilers[$compiler];
            }
        }

        throw new CompilerNotFoundException("Any of the given compilers is supported.");
    }
}