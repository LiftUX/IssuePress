var Router = Backbone.Router.extend({

  routes: {
    ':repo/new': 'newIssue',
    ':repo/:issue': 'getIssue',
    ':repo': 'getRepo',
  },

  getIssue: function( repoName, issueNum ) {

    app.verifyController( app.isRepo(repoName), function() {

      console.log("get Issue fired - issue: " + issueNum + " from the " + repoName + " repo.");
    
    }, ""); // Go Home

  },

  getRepo: function( repoName ) {

    app.verifyController( app.isRepo(repoName), function() {

      console.log("get Repo fired - repo: " + repoName );

    }, ""); // Go Home
  },

  newIssue: function( repoName ) {

    app.verifyController( app.isRepo(repoName), function() {

      console.log("make a new issue for " + repoName + " repo");

    }, ""); // Go Home
  }

});

app.Router = new Router();

app.test = function( s ) {
  console.log("logging s: " + s);
  return s;
}

app.isRepo = function( repoName ) {
  app.repoNames.each(function(repo){ 
    if( repo.get('name') === repoName ) return true;
  });
  return false;
}

// Add a layer to route 
// @param (function) vfn - conditional function the if is based on, should return true or false
// @param (function) fn - function to run if vfn passes
// @param (string) endRoute - string to pass to app.Router.navigate
app.verifyController = function( vfn, fn, endRoute ) {

  if(vfn) {
    console.log("inside vfn true");
    fn;
  } else {
    app.Router.navigate(endRoute);
  }

}

Backbone.history.start();
