<?php
use \Rackem\Rack;
require __DIR__ . '/vendor/autoload.php';

// Bullet App
$app = new Bullet\App();

// Paths / Routes
$app->path('/', function($request) use($app) {
    return $app->response("Hello World!");
});

// Response
echo $app->run(new Bullet\Request());

