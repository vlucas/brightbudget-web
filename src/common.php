<?php
// Default timezone
date_default_timezone_set('UTC');

// Setup database connection
$cfg = new \Spot\Config();
$adapter = $cfg->addConnection('development', 'mysql://root@localhost/brightbudget');
$mapper = new \Spot\Mapper($cfg);

// Add method to $app instance
$app->addMethod('mapper', function() {
    global $mapper;
    return $mapper;
});

// Evil shortcut to access $app instance anywhere
function app() {
  global $app;
  return $app;
}

// Add helper method to return base URL
$app->addMethod('url', function($path) use($app) {
  $request = $app->request();

  // Subdirectory, if any
  $subdir = trim(mb_substr($request->uri(), 0, mb_strrpos($request->uri(), $request->url())), '/');

  // Assemble full URL
  $url = $request->scheme() . '://' . $request->host() . '/' . $subdir . '/';

  // URL + path
  $url = $url . ltrim($path, '/');

  return $url . ($request->lang ? '?lang=' . $request->lang : '');
});

// Super-simple language translation by key => value array
function t($string) {
  static $lang = null;
  static $langs = array();
  if($lang === null) {
    $lang = app()->request()->get('lang', 'en');
    if(!preg_match("/^[a-z]{2}$/", $lang)) {
      throw new \Exception("Language must be a-z and only two characters");
    }
  }
  if(!isset($langs[$lang])) {
    $langFile = __DIR__ . '/lang/' . $lang . '.php';
    if(!file_exists($langFile)) {
      throw new \Exception("Language '$lang' not supported. Sorry :(");
    }
    $langs[$lang] = require($langFile);
  }

  if(isset($langs[$lang][$string])) {
    return $langs[$lang][$string];
  }
  return $string;
}
