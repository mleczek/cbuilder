<?php

namespace Mleczek\CBuilder\Version;

use DI\Container;
use Mleczek\CBuilder\Version\Providers\ConstVersionFinder;

class Factory
{
    /**
     * @var Container
     */
    private $di;

    /**
     * Factory constructor.
     *
     * @param Container $di
     */
    public function __construct(Container $di)
    {
        $this->di = $di;
    }

    /**
     * @return ConstVersionFinder
     */
    public function makeConst()
    {
        return $this->di->make(ConstVersionFinder::class);
    }
}
