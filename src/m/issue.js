var app = app || {};

// Repo Model
// ----------
// Basic model for repo, has 'issuesCollection' & github api attributes.

app.Issue = Backbone.Model.extend({

  // Default attributes
  defaults: {
    name: '',
    repo: ''
  },

  urlRoot: 'issuepress/api'
  

});
