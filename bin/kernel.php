<?php

const PROJECT_DIR = __DIR__.'/..';

// Check CWD (Current Working Directory)
if (! file_exists('cbuilder.json')) {
    echo "Working directory is not recognized as the cbuilder package, check path or create new package using 'cbuilder init' command.";
    exit(-1);
}

// Script Execution Configuration
set_time_limit(0); // disable timeout
ini_set('memory_limit', '128M');

// Composer Dependencies
require __DIR__.'/../vendor/autoload.php';

// DI Container
$builder = new DI\ContainerBuilder();
$container = $builder->build();

// Environment Variables
$env = $container->get(\Mleczek\CBuilder\System\Environment::class);
$env->setConfigDir(__DIR__.'/../config');

// Console Application
$application = new Symfony\Component\Console\Application();
$application->setName($env->config('console.name'));

foreach ($env->config('console.commands') as $command) {
    $application->add($container->make($command));
}

$application->run();
