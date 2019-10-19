<?php

namespace App;

class App
{
    public static function showPhpInfo(): bool
    {
        return phpinfo();
    }

    public static function sayHello(): string
    {
        return 'Hello World.';
    }
}
