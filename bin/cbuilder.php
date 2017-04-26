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

// Console Application
$application = new Symfony\Component\Console\Application();
$application->setName($config->get('console.name'));
$application->setVersion($config->get('console.version'));

foreach ($config->get('console.commands') as $command) {
    $application->add($container->make($command));
}

$application->run();
