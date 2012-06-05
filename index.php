<?php
// Composer Autoloader
$loader = require __DIR__ . '/vendor/autoload.php';
$loader->add('Entity', __DIR__ . '/src/'); // Entities


// Bullet App
$app = new Bullet\App();

// Evil shortcut to access $app instance anywhere
function app() {
  global $app;
  return $app;
}

// Directories
$srcDir = __DIR__ . '/src/';
$apiDir = $srcDir . '/api/';

// Common include
require $srcDir . '/common.php';

// Require all paths/routes
require $apiDir . 'index.php';
require $apiDir . 'budgets.php';

// Request
$request = new Bullet\Request();

// Response
$migrate = false;
try {
  $response = $app->run($request);

  // JSON headers and response
  if(is_array($response->content())) {
    $response->header('Content-Type', 'application/json');
    $response->content(json_encode($response->content()));
  }
} catch(\Spot\Exception_Datasource_Missing $e) {
  $migrate = true;
} catch(\PDOException $e) {
  $migrate = true;
}

// Auto migrate Entities if a database error is triggered
if($migrate === true) {
  $finder = new Symfony\Component\Finder\Finder();
  $entities = $finder->files()->name('*.php')->in($srcDir . '/Entity');
  foreach($entities as $file) {
    $app->mapper()->migrate('Entity\\' . $file->getBaseName('.php'));
  }

  $response = new Bullet\Response();
  $response->redirect($request->url(), 302);
}

echo $response;
