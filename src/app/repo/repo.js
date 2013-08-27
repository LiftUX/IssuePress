
angular.module('repo', ['AppState'])

.controller('RepoCtrl', ['$scope', '$location', '$routeParams', '$http', 'IPAppState', function($scope, $location, $routeParams, $http, IPAppState) {
  
  var ipUrl = IPAppState.IP_API_PATH;
  var repo = $routeParams.repo

  $http({
    method: 'GET',
    url: ipUrl + repo
  }).success(function(data, status, headers, config){
    if(status === 200){
      var repoData = data.data; 
      $scope.repo = repoData.repo;
      $scope.activity = repoData.activity;
      $scope.issues = repoData.issues;
      $scope.releases = repoData.releases;
    }
    console.log("Success");
    console.log(repoData);
  }).error(function(data, status, headers, config){
    console.log("Fail");
    console.log(data);
    console.log(status);
  });

}]);
