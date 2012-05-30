<?php
$app->path('budgets', function($request) use($app) {

  $mapper = spot_mapper();

  // List
  $app->get(function($request) use($mapper) {
    return array(
      '_links' => array(),
      'items' => $mapper->all('Entity\Budget')->toArray()
     );
  });

  $app->param('int', function($request, $id) use($app, $mapper) {
    // Load single record
    $item = $mapper->get('Entity\Budget', $id);

    $app->get(function($request) use($item) {
      return $item->data();
    });
  });

  return  'what?';
});
