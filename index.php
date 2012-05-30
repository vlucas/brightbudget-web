<?php
// Composer Autoloader
$loader = require __DIR__ . '/vendor/autoload.php';
$loader->add('Spot', __DIR__ . '/vendor/vlucas/spot/lib/');
$loader->add('Entity', __DIR__ . '/src/'); // Entities

// Setup database connection
$cfg = new \Spot\Config();
$adapter = $cfg->addConnection('development', 'mysql://root@localhost/brightbudget');
$mapper = new \Spot\Mapper($cfg);
function spot_mapper() {
    global $mapper;
    return $mapper;
}


// Bullet App
$app = new Bullet\App();

// Require all paths/routes
$apiDir = __DIR__ . '/src/api/';
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
  $entities = $finder->files()->name('*.php')->in(__DIR__ . '/src/Entity');
  foreach($entities as $file) {
    spot_mapper()->migrate('Entity\\' . $file->getBaseName('.php'));
  }

  $response = new Bullet\Response();
  $response->redirect($request->url(), 302);
}

echo $response;
