var frisby = require('frisby');
var app = {};

// Config
app.cfg = {
  base_url: 'http://localhost/brightb.it/budgetapp/'
};

// Helper method for creating a new budget item to work with
app.withBudget = function(userCallback) {

  // Start at base URL for API
  frisby.create('Explore from base URL')
    .get(app.cfg.base_url)
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

          // DONE callback
          var doneCallback = function() {
            // DELETE item we created
            frisby.create('Delete budget item we created')
              ._request(budget._links.delete.method, budget._links.delete.href)
              .expectStatus(200)
            .toss();
          };

          // USER callback
          userCallback.call(this, budget, doneCallback);
        })
      .toss();
    })
  .toss();

};

// Exports
exports.frisby = frisby;
exports.cfg = app.cfg;
exports.withBudget = app.withBudget;
