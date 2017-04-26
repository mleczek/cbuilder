<?php

return [

    /**
     * Project name.
     */
    'name' => 'CBuilder - Package Manager for C/C++',

    /**
     * CLI version using semantic versioning.
     *
     * @link http://semver.org/
     */
    'version' => '1.0.0',

    /**
     * Available commands via cli.
     *
     * @link https://github.com/symfony/console
     */
    'commands' => [
        //\Mleczek\CBuilder\Console\Commands\InitCommand::class,
        //\Mleczek\CBuilder\Console\Commands\BuildCommand::class,
        \Mleczek\CBuilder\Console\Commands\CleanCommand::class,
        //\Mleczek\CBuilder\Console\Commands\RebuildCommand::class,
        //\Mleczek\CBuilder\Console\Commands\RunCommand::class,
        //\Mleczek\CBuilder\Console\Commands\ScriptCommand::class,
        //\Mleczek\CBuilder\Console\Commands\LinkCommand::class,
        //\Mleczek\CBuilder\Console\Commands\UnlinkCommand::class,
    ],

];
