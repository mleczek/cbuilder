<?php

return [

    /**
     * File name which includes all the installed packages.
     */
    'autoload_file' => 'autoload.h',

    /**
     * Directory in which all dependencies will be stored.
     * The path is extended using the module name.
     *
     * Example: <dir>/org/package/...
     */
    'output_dir' => 'cmodules',

    /**
     * Directory in which additional information
     * about installed packages are stored.
     */
    'meta_dir' => '.meta',

    /**
     * File in which information about installed packages are stored
     * (file is placed inside <output_dir>/<meta_dir> directory).
     */
    'installed_lock' => 'installed.lock',

];
