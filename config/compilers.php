<?php

return [

    /*
     * Artifacts output directory which contains
     * subdirectories representing architectures
     *
     * Example: <output>/x86/org.package.a
     */
    'output' => 'build',

    /*
     * Compilers providers, not all compilers listed
     * here will be available in build process.
     */
    'providers' => [
        // 'gcc' => \Mleczek\CBuilder\Compiler\Providers\GCC::class,
    ],

];
