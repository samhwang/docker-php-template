<?php

require_once __DIR__ . '/autoload.php';

use DI\Container;

$container = new Container();
$app = $container->get('App\App');
echo $app->sayHello();
