
angular.module('repo', ['AppState'])

.controller('RepoCtrl', ['$scope', '$location', '$routeParams', '$http', 'IPData', 'IPAppState', function($scope, $location, $routeParams, $http, IPData, IPAppState) {
  
  $scope.repo = $routeParams.repo;

  if(!IPAppState.isIPRepo($scope.repo)) {
    $location.path('/404');
  }

  // Call to IPData service to populate data
  // Checks Cache before making an API call
  IPData.getRepoData($scope.repo).then(function(data){
    if(data){
      $scope.issues = data.issues;
      $scope.activity = data.activity;
      console.log(data.activity);
    }
  });


}]);
