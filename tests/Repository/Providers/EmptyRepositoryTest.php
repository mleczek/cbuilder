<?php

namespace Mleczek\CBuilder\Tests\Repository\Providers;

use Mleczek\CBuilder\Repository\Exceptions\PackageNotFoundException;
use Mleczek\CBuilder\Repository\Exceptions\RepositorySourceNotExistsException;
use Mleczek\CBuilder\Repository\Providers\EmptyRepository;
use Mleczek\CBuilder\Tests\TestCase;

class EmptyRepositoryTest extends TestCase
{
    public function testHas()
    {
        $repo = new EmptyRepository();

        $this->assertFalse($repo->has('org/package'));
        $this->assertFalse($repo->has('always/false'));
    }

    public function testGet()
    {
        $repo = new EmptyRepository();

        $this->expectException(PackageNotFoundException::class);
        $repo->get('always/throws');
    }
}
