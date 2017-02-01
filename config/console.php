<?php

return [

    'name' => 'CBuilder - Package Manager for C++',

    'commands' => [
        \Mleczek\CBuilder\Commands\Clean::class,
        \Mleczek\CBuilder\Commands\Build::class,
    ],

];