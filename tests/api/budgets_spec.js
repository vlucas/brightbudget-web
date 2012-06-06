var frisby = require('frisby');
var base_url = 'http://localhost/brightb.it/budgetapp/';

// Placeholders for fetched object
var base;
var budget;

// Start at base URL for API
frisby.create('Explore from base URL')
  .get(base_url)
  .expectStatus(200)
  .expectJSONTypes('_links', {
    budgets: Object,
  })
  .afterJSON(function(res) {
    var base = res;

    // CREATE item
    frisby.create('Create new budget')
      .post(base._links.budgets.href, {
        name: 'Rent',
        amount: 500
      })
      .expectStatus(201)
      .expectJSONTypes({
        id: Number,
        name: String,
        amount: Number,
        balance: Number,
        _links: Object
      })
      .afterJSON(function(res) {
        // Budget item
        budget = res;

        // LIST budget items to ensure our returned budget object exists in list
        frisby.create('Ensure our new budget object exists in listing')
          .get(base._links.budgets.href)
          .expectStatus(200)
          .expectJSONTypes('items.*', {
            id: Number,
            name: String,
            amount: Number,
            balance: Number,
            _links: Object
          })
          .expectJSON('items.?', budget)
          .afterJSON(function(listing) {

            // DELETE item we created
            frisby.create('Delete budget item we created')
              ._request(budget._links.delete.method, budget._links.delete.href)
              .expectStatus(200)
            .toss();

          })
        .toss();
      })
    .toss();
  })
.toss();

