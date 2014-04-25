
angular.module('AppState', [])

.factory('IPAppState', function(){
  var appState = window.IP_Vars;

  appState.getOwner = function(repo){
    
    var owner = '';
    appState.repos.forEach(function(v,i){
      if(v.name === repo)
        owner = v.owner;
    });

    return owner; 
  };

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

    var owner = IPAppState.getOwner(repo);
    repoData = data[owner + '/' + repo];

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

    var owner = IPAppState.getOwner(repo);
    var issues = data[owner + '/' + repo].issues;
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
      var org = IPAppState.getOwner(repo);
      return $http.get(ipUrl + org + '/' + repo).then(function(result) {
        return result.data; 
      });
    },

    issue: function(repo, issue) {
      var org = IPAppState.getOwner(repo);
      return $http.get(ipUrl + org + '/' + repo + '/' + issue).then(function(result) { 
        return result.data; 
      });
    },

    issueNew: function(repo, issueData) {
      var org = IPAppState.getOwner(repo);
      return $http.post(ipUrl + org + '/' + repo , issueData).then(function(result) { 
        return result.data; 
      });
    },
  
    issueComment: function(repo, issue, commentData) {
      var org = IPAppState.getOwner(repo);
      return $http.post(ipUrl + org + '/' + repo + '/' + issue, commentData).then(function(result) { 
        return result.data; 
      });
    },

    search: function(search){
      if(search.repo !== 'all') {
        search.repo = IPAppState.getOwner(search.repo) + '/' + search.repo;
      }
      console.log(search.repo);
      return $http.post(ipUrl + 'search/', search).then(function(result){
        return result.data;
      });
    },

  };

  return api;

}]);
