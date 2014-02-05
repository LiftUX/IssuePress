
angular.module('repo', ['AppState'])

.controller('RepoCtrl', ['$scope', '$location', '$routeParams', '$http', 'IPData', function($scope, $location, $routeParams, $http, IPData) {
  
  $scope.repo = $routeParams.repo;

  // Call to IPData service to populate data
  // Checks Cache before making an API call
  IPData.getRepoData($scope.repo).then(function(data){
    if(data){
      console.log(data);
      $scope.issues = data.issues;
      $scope.activity = data.activity;
    }
  });


}]);
