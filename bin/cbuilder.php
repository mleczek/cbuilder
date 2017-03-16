<?php

require __DIR__ . '/bootstrap.php';

// Script Execution Configuration
set_time_limit(0); // disable timeout
ini_set('memory_limit', '128M');

// DI Container
$builder = new DI\ContainerBuilder();
$builder->addDefinitions(CBUILDER_DIR . '/config/container.php');
$container = $builder->build();

// Environment Variables
$config = $container->get(\Mleczek\CBuilder\Environment\Config::class);
$config->setDir(CBUILDER_DIR . '/config');

// Check CWD (Current Working Directory)
if (!file_exists($config->get('package.filename'))) {
    echo "Working directory is not recognized as the cbuilder package, check path or create new package using 'cbuilder init' command.";
    exit(-1);
}

// Console Application
$application = new Symfony\Component\Console\Application();
$application->setName($config->get('console.name'));

foreach ($config->get('console.commands') as $command) {
    $application->add($container->make($command));
}

$application->run();
