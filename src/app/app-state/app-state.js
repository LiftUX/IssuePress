angular.module('AppState', [])

.factory('IPAppState', function(){
  var appState = window.IP_Vars;
  return appState;
})


.factory('IPData', ['IPAppState', 'IPAPI', function(IPAppState, IPAPI){


  var data = IPAppState.data;

  var IPData = {},
      repoData = {},
      issueData = {};

  var hasOwnProperty = Object.prototype.hasOwnProperty,
      isEmpty = function(obj) {

        // null and undefined are "empty"
        if (obj === null) return true;

        // Assume if it has a length property with a non-zero value
        // that that property is correct.
        if (obj.length > 0)    return false;
        if (obj.length === 0)  return true;

        // Otherwise, does it have any properties of its own?
        // Note that this doesn't handle
        // toString and toValue enumeration bugs in IE < 9
        for (var key in obj) {
          if (hasOwnProperty.call(obj, key)) return false;
        }

        return true;
      };


  IPData.getRepoData = function(repo){

    // Take a look at what we had cached
    data.forEach(function(e, i, a) {
      if(e.name === repo)
        repoData = e;
    });

    var keys = ['activity', 'issues', 'repo'];

    keys.forEach(function(e){
      if(!isEmpty(repoData[e])) {
        console.log("We have cached data for: " + repo + " " + e);
        console.log(repoData[e]);
      } else {
        console.log("We need to hit API to fetch fresh data for: " + repo + " " + e);
      }
        
    });

    return repoData;

  };

  IPData.getIssueData = function(issue, repo){
    console.log("Looking for issue data for: " + issue + " in " + repo);
  };

  return IPData;

}])


.factory('IPAPI', ['$http', 'IPAppState', function($http, IPAppState){
  
  var ipUrl = IPAppState.API_PATH;

  var api = {
    repo: function(repo){
      var apiEndpoint = ipUrl + repo;

      return $http({
        method: 'GET',
        url: apiEndpoint 
      });
    },

    issue: function(repo, issue) {
      var apiEndpoint = ipUrl + repo + '/' + issue;
      return $http({
        method: 'GET',
        url: apiEndpoint 
      });
    },
  
  };

  return api;

}]);
