<?php

return [

    'output_dir' => 'build',

    'providers' => [
        'gcc' => \Mleczek\CBuilder\Compilers\Providers\GCC::class,
    ],

];