angular.module('AppState', [])

.factory('IPAppState', function(){
  var appState = window.IP_Vars;
  return appState;
})


.factory('IPData', ['$q', 'IPAppState', 'IPAPI', function($q, IPAppState, IPAPI){


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

    repoData = data[repo];

    var keys = ['activity', 'issues', 'repo'];
    var keyTrack = [false, false, false];

    // Loop through cache for each key, check for valid content
    keys.forEach(function(e, i, a){
      if(!isEmpty(repoData[e])) {
        keyTrack[i] = true;
      }
    });

    if(keyTrack[0] && keyTrack[1] && keyTrack[2]){
      var cachedData = $q.defer();
      cachedData.resolve(repoData);

      return cachedData.promise;
    } else {
      return IPAPI.repo(repo).then(function(result){
        return result.data;
      });
    }

  };

  IPData.getIssueData = function(repo, issue){

    var issues = data[repo].issues;
    var hasIssueCached = false;

    issues.forEach(function(e, i, a){
      if(issue == e.number)
        hasIssueCached = i;
    });

    if(hasIssueCached !== false) {

      var cachedData = {};
      cachedData.issue = data[repo].issues[hasIssueCached];
      cachedData.comments = data[repo].comments[issue];

      var cache = $q.defer();
      cache.resolve(cachedData);

      return cache.promise;

    } else {

      return IPAPI.issue(repo, issue).then(function(result){
        return result.data;
      });

    }

    

  };

  return IPData;

}])


.factory('IPAPI', ['$http', 'IPAppState', function($http, IPAppState){
  
  var ipUrl = IPAppState.API_PATH;
  $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";

  var api = {
    repo: function(repo){
      return $http.get(ipUrl + repo).then(function(result) {
        return result.data; 
      });
    },

    issue: function(repo, issue) {
      return $http.get(ipUrl + repo + '/' + issue).then(function(result) { 
        return result.data; 
      });
    },

    issueNew: function(repo, issueData) {
      return $http.post(ipUrl + repo , issueData).then(function(result) { 
        return result.data; 
      });
    },
  
    issueComment: function(repo, issue, comment) {
      return $http.post(ipUrl + repo + '/' + issue, comment).then(function(result) { 
        return result.data; 
      });
    },

    search: function(search){
      return $http.post(ipUrl + 'search/', search).then(function(result){
        return result.data;
      });
      
    },

  };

  return api;

}]);
