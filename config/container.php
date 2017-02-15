<?php

use DI\Scope;

return [

    \Mleczek\CBuilder\Repositories\Providers\LocalRepository::class => DI\object()->scope(Scope::PROTOTYPE),
    \Mleczek\CBuilder\Downloaders\Providers\SymlinkDownloader::class => DI\object()->scope(Scope::PROTOTYPE),
    \Mleczek\CBuilder\Versions\Providers\ConstVersion::class => DI\object()->scope(Scope::PROTOTYPE),

];
