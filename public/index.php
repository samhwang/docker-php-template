<?php

require_once __DIR__ . '/autoload.php';

use App\App;

$app = new App();
echo $app->sayHello();
