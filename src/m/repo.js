var app = app || {};

// Repo Model
// ----------
// Basic model for repo, has 'issuesCollection' & github api attributes.

app.Repo = Backbone.Model.extend({

  // Default attributes
  defaults: {
    name: '',
    repoIssues: ''
  },

  initialize: function(i) {
  },

  urlRoot: 'issuepress/api'
  

});
