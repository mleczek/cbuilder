<?php

return [

    /*
     * List of repositories supported in the cbuilder.json file.
     */
    'providers' => [
        'local' => \Mleczek\CBuilder\Repository\Providers\LocalRepository::class,
    ],

    /*
     * Default repositories added at the end of the list of package repositories.
     */
    'defaults' => [
        // ...
    ]

];
