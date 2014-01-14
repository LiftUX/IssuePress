angular.module('AppState', [])

.factory('IPAppState', function(){
  var appState = window.IP_Vars;
  return appState;
})


.factory('IPData', ['IPAppState', function(IPAppState){


  var data = IPAppState.IP_data;

  var IPData = {};

  IPData.getRepoData = function(repo){
    console.log("Looking for repo data for: " + repo);
  };

  IPData.getIssueData = function(issue, repo){
    console.log("Looking for issue data for: " + issue + " in " + repo);
  };


  return IPData;
  
}])


.factory('IPAPI', ['$http', 'IPAppState', function($http, IPAppState){
  
  var ipUrl = IPAppState.IP_API_PATH;

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
