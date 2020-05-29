<?php

/**
 * PHP version 7.4
 *
 * @package Project_Name
 * @author  Sam Huynh <samhwang2112.dev@gmail.com>
 */

namespace App;

/**
 * Main App class
 */
class App
{
    /**
     * Show PHP Info function
     * 
     * @return bool
     */
    public function showPhpInfo(): bool
    {
        return phpinfo();
    }

    /**
     * Say "Hello" function
     * 
     * @return string
     */
    public function sayHello(): string
    {
        return 'Hello World.';
    }
}
