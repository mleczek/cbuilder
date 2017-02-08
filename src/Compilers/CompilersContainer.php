<?php


namespace Mleczek\CBuilder\Compilers;


use Closure;
use DI\Container;
use Mleczek\CBuilder\Compilers\Compiler;
use Mleczek\CBuilder\Compilers\Exceptions\NotSupportedException;

/**
 * Stores information about available compilers,
 * allows registering and searching compilers.
 *
 * @Injectable
 */
class CompilersContainer
{
    /**
     * @var Compiler[]
     */
    private $compilers = [];

    /**
     * @Inject
     * @var Container
     */
    protected $container;

    /**
     * Get the most suitable compiler.
     *
     * @return Compiler
     */
    public function getOne()
    {
        $compilers = array_keys($this->compilers);

        return $this->getOneOf($compilers);
    }

    /**
     * Get the most suitable compiler of the given set.
     *
     * @param array|string $names
     * @return Compiler
     * @throws \Mleczek\CBuilder\Compilers\Exceptions\NotSupportedException
     */
    public function getOneOf($names)
    {
        $names = (array)$names;

        foreach ($names as $name) {
            // Check whether driver was registered and successfully loaded
            if (array_key_exists($name, $this->compilers) && $this->compilers[$name]->isSupported()) {
                return $this->compilers[$name];
            }
        }

        throw new NotSupportedException(/* TODO: Exception message */);
    }

    /**
     * Register the compiler under specific name.
     *
     * The $callback variable must return instance of
     * Mleczek\CBuilder\Compilers\Contracts\Compiler interface.
     *
     * @param string $name
     * @param Closure|string $driver
     * @return bool True if driver is supported, false otherwise.
     */
    public function register($name, $driver)
    {
        if ($driver instanceof Closure) {
            $this->compilers[$name] = $this->container->call($driver);
        } else {
            $this->compilers[$name] = $this->container->make($driver);
        }
        
        return $this->compilers[$name]->isSupported();
    }
}