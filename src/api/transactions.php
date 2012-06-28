<?php
use Entity\Transaction;

// Can only be used nested inside 'budget' path with loaded $budget object
if(!isset($budget)) {
  throw new \Exception("Budget not found", 404);
}

$app->path('transactions', function($request) use($app, $budget) {

  $mapper = $app->mapper();

  // List
  $app->get(function($request) use($app, $mapper, $budget) {
    return array(
      //'_links' => array(
        //'add' => array(
          //'title' => t('Add Transaction'),
          //'href' => $app->url('budgets/' . $budget->id . '/transactions'),
          //'method' => 'post',
          //'parameters' => Transaction::parameters()
        //)
      //),
      'items' => $budget->transactions->toArray()
     );
  });

  // POST - New Item
  $app->post(function($request) use($app, $mapper, $budget) {
    $transaction = new Transaction($request->post());
    $transaction->budget_id = $budget->id;
    if($mapper->save($transaction)) {
      return $app->response($transaction->toArray(), 201);
    } else {
      return $app->response(array('errors' => $transaction->errors()), 400);
    }
    return $transaction->data();
  });

  // Single record
  $app->param('int', function($request, $id) use($app, $mapper) {
    // Load single record
    $transaction = $mapper->get('Entity\Transaction', $id);
    if(!$transaction) {
      return false;
    }

    // GET
    $app->get(function($request) use($transaction) {
      return $transaction->toArray();
    });

    // DELETE
    $app->delete(function($request) use($app, $mapper, $transaction) {
      $result = $mapper->delete($transaction);
      if($result !== false) {
        return $app->response($transaction, 200);
      }
      return 404;
    });
  });
});
