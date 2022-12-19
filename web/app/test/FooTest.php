<?php

namespace AppTest\Acme;

use App\Acme\Util;
use PHPUnit\Framework\TestCase;

class FooTest extends TestCase
{
    public function testGetName()
    {
        $foo = new Util();
        $this->assertEquals($foo->getName(), 'Nginx PHP MySQL');
    }
}
