<?php
use Entity\Budget;

$app->path('budgets', function($request) use($app) {

  $mapper = $app->mapper();

  // List
  $app->get(function($request) use($app, $mapper) {
    return array(
      '_links' => array(
        'add' => array(
          'title' => t('Add Budget'),
          'href' => $app->url('budgets'),
          'method' => 'post',
          'parameters' => Entity\Budget::parameters()
        )
      ),
      'items' => $mapper->all('Entity\Budget')->toArray()
     );
  });

  // POST - New Item
  $app->post(function($request) use($app, $mapper) {
    $budget = new Budget($request->post());
    if($mapper->save($budget)) {
      return $app->response($budget->toArray(), 201);
    } else {
      return $app->response(array('errors' => $budget->errors()), 400);
    }
    return $budget->data();
  });

  // Single record
  $app->param('int', function($request, $id) use($app, $mapper) {
    // Load single record
    $budget = $mapper->get('Entity\Budget', $id);
    if(!$budget) {
      return false;
    }

    // GET
    $app->get(function($request) use($budget) {
      $data = $budget->toArray();
      $data['items'] = $budget->transactions->toArray();
      return $data;
    });

    // DELETE
    $app->delete(function($request) use($app, $mapper, $budget) {
      $result = $mapper->delete($budget);
      if($result !== false) {
        return $app->response($budget, 200);
      }
      return 404;
    });

    // Transactions nested path
    require __DIR__ . '/transactions.php';
  });
});
