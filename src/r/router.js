var Router = Backbone.Router.extend({

  routes: {
    ':repo/new': 'newIssue',
    ':repo/:issue': 'getIssue',
    ':repo': 'getRepo',
  },

  getIssue: function( repoName, issueNum ) {
    console.log("get Issue fired - issue: " + issueNum + " from the " + repoName + " repo.");
  },

  getRepo: function( repoName ) {
    console.log("get repo fired - the " + repoName + " repo");
  },

  newIssue: function( repoName ) {
    console.log("make a new issue for " + repoName + " repo");
  }

});

app.Router = new Router();
Backbone.history.start();
