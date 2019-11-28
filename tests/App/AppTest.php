<?php

/**
 * PHP version 7.4
 *
 * @package Project_Name
 * @author  Sam Huynh <samhwang2112.dev@gmail.com>
 */

namespace App;

use PHPUnit\Framework\TestCase;
use App\App;

/**
 * Main App Test class
 */
class AppTest extends TestCase
{
    /**
     * Test saying Hello function
     * 
     * @return void
     */
    public function testHello(): void
    {
        $this->assertEquals('Hello World.', App::sayHello());
    }
}
