<?php
$app->path('/', function($request) use($app) {
  $app->get(function($request) use($item) {

    return array(
      '_links' => array(
        'budgets' => array('href' => app()->url('/budgets/'))
      )
    );
  });
});
