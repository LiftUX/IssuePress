var app = app || {};

// Repo Collection
// ---------------

app.ReposList = Backbone.Collection.extend({
  model: app.Repo,

  initialize: function(){
  },

});

app.repoNames = new app.ReposList(window.IP_repos);

