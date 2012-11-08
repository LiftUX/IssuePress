var app = app || {};

// Issue Collection
// ---------------

app.IssuesList = new Backbone.Collection({
  model: app.Issue,
});
