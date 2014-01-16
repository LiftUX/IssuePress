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

        if (obj === null) return true;
        if (obj.length > 0)    return false;
        if (obj.length === 0)  return true;

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
    var keyTrack = [false, false, false];

    // Loop through cache for each key, check for valid content
    keys.forEach(function(e, i, a){
      if(!isEmpty(repoData[e])) {
        console.log("We have cached data for: " + repo + " " + e);
        console.log(repoData[e]);
        keyTrack[i] = true;
      } else {
        console.log("We need to hit API to fetch fresh data for: " + repo + " " + e);
      }
    });

    if(keyTrack[0] && keyTrack[1] && keyTrack[2]){
      return repoData;
    } else {
      return IPAPI.repo(repo).success(repoHandler);
    }

    var repoHandler = function(data, status, headers, config){
      if(status == 200) {
        console.log(data);
        repoData.issues = data.data.issues;
        repoData.activity = data.data.activity;
        //repoData.releases = data.data.releases;

        return repoData;
      }
    };


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
