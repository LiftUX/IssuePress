
angular.module('repo', ['AppState'])

.controller('RepoCtrl', ['$scope', '$location', '$routeParams', '$http', 'IPAppState', function($scope, $location, $routeParams, $http, IPAppState) {
  
  $scope.repo = $routeParams.repo;

  var ipUrl = IPAppState.IP_API_PATH;
  var repo = $scope.repo; 

  $http({
    method: 'GET',
    url: ipUrl + repo
  }).success(function(data, status, headers, config){
    if(status === 200){
      var repoData = data.data; 
      $scope.issues = repoData.issues;
      $scope.activity = repoData.activity;
//      $scope.releases = repoData.releases;
    }
    console.log("Success");
    console.log(repoData);
  }).error(function(data, status, headers, config){
    console.log("Fail");
    console.log(data);
    console.log(status);
  });

}]);
