<?php

/**
 * PHP version 7.4
 *
 * @package Project_Name
 * @author  Sam Huynh <samhwang2112.dev@gmail.com>
 */

namespace App;

use PHPUnit\Framework\TestCase;
use DI\Container;

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
        $container = new Container();
        $app = $container->get('App\App');
        $this->assertEquals('Hello World.', $app->sayHello());
    }
}
