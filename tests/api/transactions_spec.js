var app = require('./_common');

// Helper to get budget and cleanup afterwards
app.withBudget(function(budget, doneCallback) {

  // List transactions
  app.frisby.create('List transactions')
    ._request(budget._links.transactions.method, budget._links.transactions.href)
    .expectStatus(200)
    .expectJSONTypes('_links', {
      add: Object
    })
    .afterJSON(function(res) {
      var base = res;

      // CREATE transaction
      app.frisby.create('Create transaction')
        ._request(res._links.add.method, res._links.add.href, {
          name: 'Apartment',
          amount: 470
        })
        .expectStatus(201)
        .afterJSON(function(res) {
          var transaction = res;

          // LIST transactions to ensure our transaction object exists in list
          app.frisby.create('Ensure our new transaction object exists in listing')
            .get(base._links.add.href)
            .expectStatus(200)
            .expectJSONTypes('items.*', {
              id: Number,
              name: String,
              amount: Number,
              _links: Object
            })
            .expectJSON('items.?', transaction)
            .afterJSON(function(listing) {

              // DELETE item we created
              app.frisby.create('Delete budget item we created')
                ._request(transaction._links.delete.method, transaction._links.delete.href)
                .expectStatus(200)
                .after(function(err, body, res) {

                  // DONE with budget item
                  doneCallback.call(this);

                })
              .toss();

            })
          .toss();

        })
      .toss();

    })
  .toss();

});
