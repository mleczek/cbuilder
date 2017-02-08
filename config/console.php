<?php

return [

    'name' => 'CBuilder - Package Manager for C++',

    'commands' => [
        \Mleczek\CBuilder\Console\Commands\Init::class,
        \Mleczek\CBuilder\Console\Commands\Build::class,
        \Mleczek\CBuilder\Console\Commands\Clean::class,
        \Mleczek\CBuilder\Console\Commands\Rebuild::class,
        \Mleczek\CBuilder\Console\Commands\Run::class,
    ],

];