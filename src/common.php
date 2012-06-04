<?php

// Setup database connection
$cfg = new \Spot\Config();
$adapter = $cfg->addConnection('development', 'mysql://root@localhost/brightbudget');
$mapper = new \Spot\Mapper($cfg);

// Add method to $app instance
$app->addMethod('mapper', function() {
    global $mapper;
    return $mapper;
});

// Add helper method to return base URL
$app->addMethod('url', function($path) use($app) {
  $request = $app->request();

  // Subdirectory, if any
  $subdir = trim(mb_substr($request->uri(), 0, mb_strrpos($request->uri(), $request->url())), '/');

  // Assemble full URL
  $url = $request->scheme() . '://' . $request->host() . '/' . $subdir . '/';

  // Reutrn URL + path
  return $url . ltrim($path, '/');
});

// Super-basic language translation
function t($string) {
  return $string;
}
