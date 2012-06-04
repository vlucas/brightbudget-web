var frisby = require('frisby');
var base_url = 'http://localhost/brightb.it/budgetapp/';

// CREATE item
frisby.create('Create new budget')
  .post(base_url + 'budgets', {
    name: 'Rent',
    amount: 500
  })
  .expectStatus(201)
  .expectJSONTypes({
    id: Number,
    name: String,
    amount: Number,
    _links: Object
  })
  .afterJSON(function(res) {
    // Budget item
    var budget = res;

    // DELETE item we just created
    frisby.create('Delete budget item we just created')
      ._request(res._links.delete.method, res._links.delete.href)
      .expectStatus(204)
    .toss();

  })
.toss();

