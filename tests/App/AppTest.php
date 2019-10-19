<?php

namespace App;

use PHPUnit\Framework\TestCase;
use App\App;

class AppTest extends TestCase
{
    public function testHello()
    {
        $this->assertEquals('Hello World.', App::sayHello());
    }
}
