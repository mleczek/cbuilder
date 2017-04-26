#!/usr/bin/env php
<?php

// [REQUIREMENTS]
// Allow write-access for the phar archives,
// open php.ini and set phar.readonly to off.

Phar::mapPhar('cbuilder.phar');

$basePath = 'phar://'.__FILE__.'/';
require $basePath.'bin/cbuilder.php';

__HALT_COMPILER();
