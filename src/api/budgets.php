<?php
use Entity\Budget;

$app->path('budgets', function($request) use($app) {

  $mapper = $app->mapper();

  // List
  $app->get(function($request) use($app, $mapper) {
    return array(
      '_links' => array(
        'add' => array(
          'href' => $app->url('budgets'),
          'method' => 'post',
          'parameters' => Entity\Budget::fields()
        )
      ),
      'items' => $mapper->all('Entity\Budget')->toArray()
     );
  });

  // POST - New Item
  $app->post(function($request) use($app, $mapper) {
    $item = new Budget($request->post());
    if($mapper->save($item)) {
      return $app->response($item->toArray(), 201);
    } else {
      return $app->response($item->errors(), 400);
    }
    return $item->data();
  });

  // Single record
  $app->param('int', function($request, $id) use($app, $mapper) {
    // Load single record
    $item = $mapper->get('Entity\Budget', $id);
    if(!$item) {
      return false;
    }

    // GET
    $app->get(function($request) use($item) {
      return $item->toArray();
    });

    // DELETE
    $app->delete(function($request) use($mapper, $item) {
      $result = $mapper->delete($item);
      if($result !== false) {
        return 204;
      }
      return 404;
    });
  });
});
