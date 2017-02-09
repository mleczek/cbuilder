<?php

// Load phar config file
$config = (object)(require __DIR__ .'/../config/phar.php');

// Archive project directory as phar file
$archive = new Phar($config->output);
$archive->buildFromDirectory('.');
$archive->setStub(file_get_contents('bin/phar-bootstrap.php'));