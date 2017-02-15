<?php

namespace Mleczek\CBuilder\Repositories;

interface Repository
{
    /**
     * @param string $package
     * @param string $constraint Version constraint.
     * @return bool
     */
    public function has($package, $constraint = '*');

    /**
     * @param string $package
     * @param string $constraint Version constraint.
     */
    public function download($package, $constraint);
}
