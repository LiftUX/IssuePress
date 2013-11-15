
angular.module('repo', ['AppState'])

.controller('RepoCtrl', ['$scope', '$location', '$routeParams', '$http', 'IPAPI', function($scope, $location, $routeParams, $http, IPAPI) {
  
  $scope.repo = $routeParams.repo;

  var handleData = function(data, status, headers, config){
    if(status = 200) {
      console.log(data);
      $scope.issues = data.data.issues;
      $scope.activity = data.data.activity;
      //$scope.releases = data.data.releases;
    }
  };

  IPAPI.repo($scope.repo).success(handleData);


}]);
