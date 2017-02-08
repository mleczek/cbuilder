<?php

return [

    'outputDir' => 'build',

    'drivers' => [
        'gcc' => \Mleczek\CBuilder\Compilers\Providers\GCC::class,
    ],

];