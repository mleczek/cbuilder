<?php

// [REQUIREMENTS]
// Allow write-access for the phar archives,
// open php.ini and set phar.readonly to off.

Phar::mapPhar();

$basePath = 'phar://'.__FILE__.'/';
require $basePath.'bin/kernel.php';

__HALT_COMPILER();
