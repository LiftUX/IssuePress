angular.module('AppState', [])

.factory('IPAppState', function(){
  var appState = window.IP_Vars;
  return appState;
})


.factory('IPData', ['IPAppState', function(IPAppState){

  var data = {};
  return data;
  
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
