<?php

// Check CWD (Current Working Directory)
if (!file_exists('cbuilder.json')) {
    // FIXME: Uncomment below lines (commented for easier developing)
    //echo "Current directory is not recognized as the cbuilder package, check path or create new package using 'cbuilder init' command.";
    //exit(-1);
}

// Script Execution Configuration
set_time_limit(0); // disable timeout
ini_set('memory_limit', '128M');

// Composer Dependencies
require __DIR__ . '/../vendor/autoload.php';

// DI Container
$builder = new DI\ContainerBuilder();
$builder->useAnnotations(true);
$container = $builder->build();

// Console Configuration
$config = $container->get(\Mleczek\CBuilder\Configuration::class);
$config->setDir(__DIR__ . '/../config');

// Console Application
$application = new Symfony\Component\Console\Application();
$application->setName($config->get('console.name'));

foreach ($config->get('console.commands') as $command) {
    $application->add($container->make($command));
}

$application->run();